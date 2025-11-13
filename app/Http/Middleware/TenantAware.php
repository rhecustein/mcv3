<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantAware
{
    /**
     * Handle an incoming request.
     *
     * Identifies tenant from subdomain and sets it globally for the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Extract subdomain from host
        // Example: kimiafarma.mcv3.local -> kimiafarma
        $subdomain = $this->extractSubdomain($host);

        // Skip tenant resolution for main domain or if no subdomain
        if (empty($subdomain) || $subdomain === 'www') {
            return $next($request);
        }

        // Find tenant by slug (subdomain)
        $tenant = Tenant::where('slug', $subdomain)
            ->where('is_active', true)
            ->first();

        // Handle tenant not found
        if (!$tenant) {
            return response()->view('errors.tenant-not-found', [
                'subdomain' => $subdomain
            ], 404);
        }

        // Check if tenant subscription is valid
        if (!$tenant->hasValidSubscription()) {
            return response()->view('errors.subscription-expired', [
                'tenant' => $tenant
            ], 403);
        }

        // Set tenant in application container (globally available)
        app()->instance('tenant', $tenant);

        // Set tenant ID in config for easy access
        config(['app.tenant_id' => $tenant->id]);
        config(['app.tenant' => $tenant]);

        // Share tenant with all views
        view()->share('tenant', $tenant);

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function extractSubdomain(string $host): ?string
    {
        // Remove port if present
        $host = explode(':', $host)[0];

        // Split by dot
        $parts = explode('.', $host);

        // Need at least subdomain.domain.tld (3 parts)
        if (count($parts) < 3) {
            return null;
        }

        // Return first part as subdomain
        return $parts[0];
    }
}

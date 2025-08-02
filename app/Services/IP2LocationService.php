<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IP2LocationService
{
    protected string $endpoint = 'https://api.ip2location.io/';
    protected string $apiKey;
    
    // Fallback data untuk development dan testing
    private const FALLBACK_LOCATIONS = [
        '127.0.0.1' => [
            'ip' => '127.0.0.1',
            'country' => 'Indonesia',
            'province' => 'Kepulauan Riau',
            'city' => 'Batam',
            'latitude' => 1.0456,
            'longitude' => 104.0305,
        ],
        'localhost' => [
            'ip' => 'localhost',
            'country' => 'Indonesia',
            'province' => 'Kepulauan Riau',
            'city' => 'Batam',
            'latitude' => 1.0456,
            'longitude' => 104.0305,
        ],
    ];

    public function __construct()
    {
        $this->apiKey = env('IP2LOCATIONIO_API_KEY', '');

        if (empty($this->apiKey)) {
            Log::warning('IP2Location API key not set in .env file');
        }
    }

    /**
     * Get location with caching and fallback support
     */
    public function getLocation(string $ip): array
    {
        // Check cache first
        $cacheKey = "ip_location:{$ip}";
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            Log::debug('IP location retrieved from cache', ['ip' => $ip]);
            return $cached;
        }

        // Handle localhost and private IPs
        if ($this->isLocalIp($ip)) {
            $location = $this->getLocalFallback($ip);
            Cache::put($cacheKey, $location, now()->addHours(24));
            return $location;
        }

        // If no API key, return fallback
        if (empty($this->apiKey)) {
            Log::warning('IP2Location API key not configured, using fallback', ['ip' => $ip]);
            return $this->getFallbackLocation($ip);
        }

        try {
            // Make API request with timeout
            $response = Http::timeout(5)
                ->retry(2, 100)
                ->get($this->endpoint, [
                    'key' => $this->apiKey,
                    'ip'  => $ip,
                ]);

            if (!$response->successful()) {
                Log::warning('IP2Location API request failed', [
                    'ip' => $ip,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return $this->getFallbackLocation($ip);
            }

            $data = $response->json();
            
            // Validate response data
            if (empty($data) || isset($data['error'])) {
                Log::warning('IP2Location API returned error', [
                    'ip' => $ip,
                    'error' => $data['error'] ?? 'Empty response'
                ]);
                
                return $this->getFallbackLocation($ip);
            }

            $location = [
                'ip'        => $data['ip'] ?? $ip,
                'country'   => $data['country_name'] ?? 'Indonesia',
                'province'  => $data['region_name'] ?? 'UNKNOWN',
                'city'      => $data['city_name'] ?? 'UNKNOWN',
                'latitude'  => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'timezone'  => $data['time_zone'] ?? 'Asia/Jakarta',
                'isp'       => $data['isp'] ?? 'UNKNOWN',
            ];

            // Cache successful response for 24 hours
            Cache::put($cacheKey, $location, now()->addHours(24));
            
            Log::info('IP location retrieved successfully', [
                'ip' => $ip,
                'location' => $location['city'] . ', ' . $location['province']
            ]);

            return $location;

        } catch (\Throwable $e) {
            Log::error('IP2Location service error', [
                'ip' => $ip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getFallbackLocation($ip);
        }
    }

    /**
     * Check if IP is local/private
     */
    private function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost']) ||
               filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Get fallback location for local IPs
     */
    private function getLocalFallback(string $ip): array
    {
        if (isset(self::FALLBACK_LOCATIONS[$ip])) {
            return self::FALLBACK_LOCATIONS[$ip];
        }

        // Default fallback for any local IP
        return [
            'ip'        => $ip,
            'country'   => 'Indonesia',
            'province'  => 'Kepulauan Riau',
            'city'      => 'Batam',
            'latitude'  => 1.0456,
            'longitude' => 104.0305,
            'timezone'  => 'Asia/Jakarta',
            'isp'       => 'Local Network',
        ];
    }

    /**
     * Get fallback location when API fails
     */
    private function getFallbackLocation(string $ip): array
    {
        // For production, we should return UNKNOWN
        // But for development/staging, we can use safe defaults
        $environment = app()->environment();
        
        if (in_array($environment, ['local', 'development', 'staging'])) {
            return [
                'ip'        => $ip,
                'country'   => 'Indonesia',
                'province'  => 'Kepulauan Riau', // Default to allowed province for testing
                'city'      => 'Batam',
                'latitude'  => 1.0456,
                'longitude' => 104.0305,
                'timezone'  => 'Asia/Jakarta',
                'isp'       => 'UNKNOWN',
            ];
        }

        // For production, return unknown values
        return [
            'ip'        => $ip,
            'country'   => 'UNKNOWN',
            'province'  => 'UNKNOWN',
            'city'      => 'UNKNOWN',
            'latitude'  => null,
            'longitude' => null,
            'timezone'  => 'Asia/Jakarta',
            'isp'       => 'UNKNOWN',
        ];
    }

    /**
     * Clear cached location for an IP
     */
    public function clearCache(string $ip): void
    {
        $cacheKey = "ip_location:{$ip}";
        Cache::forget($cacheKey);
        
        Log::info('Cleared IP location cache', ['ip' => $ip]);
    }

    /**
     * Get all cached locations (for debugging)
     */
    public function getCachedLocations(): array
    {
        // This is a simplified version - in production you might want to use Cache tags
        $cached = [];
        
        // Note: This won't work with all cache drivers
        // It's mainly for debugging purposes
        if (method_exists(Cache::getStore(), 'getPrefix')) {
            $prefix = Cache::getStore()->getPrefix();
            // Implementation depends on cache driver
        }
        
        return $cached;
    }

    /**
     * Validate location data
     */
    public function isValidLocation(array $location): bool
    {
        return !empty($location['province']) && 
               $location['province'] !== 'UNKNOWN' &&
               !empty($location['country']);
    }

    /**
     * Get location from coordinates (reverse geocoding)
     */
    public function getLocationFromCoordinates(float $latitude, float $longitude): array
    {
        // This is a placeholder - implement if needed
        // You might want to use a different API for reverse geocoding
        
        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'country' => 'Indonesia',
            'province' => 'UNKNOWN',
            'city' => 'UNKNOWN',
        ];
    }
}
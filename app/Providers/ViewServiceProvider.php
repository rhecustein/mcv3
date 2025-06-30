<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionLogin;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share ke semua view
        View::composer('*', function ($view) {
            $activeSessions = 0;
            $maxSessions = 3;

            if (Auth::check()) {
                $activeSessions = SessionLogin::where('user_id', Auth::id())
                    ->where('is_active', true)
                    ->count();
            }

            $view->with([
                'activeSessions' => $activeSessions,
                'maxSessions' => $maxSessions,
            ]);
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Laravel default events
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Authentication & Security Events
        \App\Events\UserLoggedIn::class => [
            \App\Listeners\HandleUserLoggedIn::class,
        ],

        \App\Events\UserLoginFailed::class => [
            \App\Listeners\HandleUserLoginFailed::class,
        ],

        \App\Events\IpBlocked::class => [
            \App\Listeners\HandleIpBlocked::class,
        ],

        \App\Events\SuspiciousActivity::class => [
            \App\Listeners\HandleSuspiciousActivity::class,
        ],

        // Additional security events can be added here
        // \App\Events\UserLoggedOut::class => [
        //     \App\Listeners\HandleUserLoggedOut::class,
        // ],

        // \App\Events\PasswordChanged::class => [
        //     \App\Listeners\HandlePasswordChanged::class,
        // ],

        // \App\Events\AccountCompromised::class => [
        //     \App\Listeners\HandleAccountCompromised::class,
        // ],

        // Application-specific events
        // \App\Events\DocumentGenerated::class => [
        //     \App\Listeners\HandleDocumentGenerated::class,
        // ],

        // \App\Events\ResultCreated::class => [
        //     \App\Listeners\HandleResultCreated::class,
        // ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Additional event listeners can be registered here programmatically
        
        // Example: Listen for model events
        // Event::listen('eloquent.created: App\Models\Result', function ($result) {
        //     // Handle result creation
        // });

        // Example: Listen for Laravel events
        // Event::listen('auth.login', function ($user, $remember) {
        //     // Additional login handling
        // });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    /**
     * Get the listener directories that should be used to discover events.
     */
    protected function discoverEventsWithin(): array
    {
        return [
            $this->app->path('Listeners'),
        ];
    }
}
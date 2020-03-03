<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\SuccessfulLoginListener',
        ],
        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\FailedLoginListener',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogSuccessfulLogout',
        ],
        'Illuminate\Auth\Events\PasswordReset' => [
            'App\Listeners\PasswordResetListener',
        ],
        'App\Events\RDsAdded' => [
            'App\Listeners\RDsAddedListener'
        ],
        'App\Events\RDsRemoved' => [
            'App\Listeners\RDsRemovedListener'
        ],
        'App\Events\EventProcessed' => [
            'App\Listeners\EventProcessedListener'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

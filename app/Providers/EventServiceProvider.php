<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * Класс, представляющий провайдер сервисов событий.
 * @package App\Providers Провайдеры приложения.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Маппинг слушателей событий для приложения.
     *
     * @var array
     */
    protected $listen = [
        // Регистрация пользователя
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Вход пользователя
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\SuccessfulLoginListener',
        ],
        // Неудачный вход пользователя
        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\FailedLoginListener',
        ],
        // Выход пользователя
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogSuccessfulLogout',
        ],
        // Сброс пароля пользователем
        'Illuminate\Auth\Events\PasswordReset' => [
            'App\Listeners\PasswordResetListener',
        ],
        // Добавление ответственного подразделения к событию
        'App\Events\RDsAdded' => [
            'App\Listeners\RDsAddedListener'
        ],
        // Удаление ответственного подразделения у события
        'App\Events\RDsRemoved' => [
            'App\Listeners\RDsRemovedListener'
        ],
        // Обработка события
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

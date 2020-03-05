<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий слушатель события сброса пользователем пароля.
 * @package App\Listeners Классы-слушатели.
 */
class PasswordResetListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Обрабатывает событие.
     *
     * @param PasswordReset $event объект события.
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        /** @var \App\User $user */
        $user = $event->user;
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("User ".$user->name." resets his password");
    }
}

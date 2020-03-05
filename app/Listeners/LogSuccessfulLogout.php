<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий слушатель события успешного выхода пользователя из приложения.
 * @package App\Listeners Классы-слушатели.
 */
class LogSuccessfulLogout implements ShouldQueue
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
     * @param Logout $event объект события.
     * @return void
     */
    public function handle(Logout $event)
    {
        /** @var \App\User $user */
        $user = $event->user;
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("User ".$user->name." successful logout");
    }
}

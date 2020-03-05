<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий слушатель события успешного входа в приложение.
 * @package App\Listeners Классы-слушатели.
 */
class SuccessfulLoginListener implements ShouldQueue
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
     * @param Login $event объект события.
     * @return void
     */
    public function handle(Login $event)
    {
        /** @var \App\User $user */
        $user = $event->user;
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("User ".$user->name." successful login");
    }
}

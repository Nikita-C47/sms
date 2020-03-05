<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий слушатель события неудачной авторизации пользователя.
 * @package App\Listeners Классы-слушатели.
 */
class FailedLoginListener implements ShouldQueue
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
     * @param Failed $event объект события.
     * @return void
     */
    public function handle(Failed $event)
    {
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("Failed login attempt for email ".$event->credentials['email']);
    }
}

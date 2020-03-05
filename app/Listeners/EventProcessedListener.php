<?php

namespace App\Listeners;

use App\Events\EventProcessed;
use App\Notifications\EventProcessedNotification;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Класс, представляющий слушатель события обработки события.
 * @package App\Listeners Классы-слушатели.
 */
class EventProcessedListener implements ShouldQueue
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
     * @param EventProcessed $event объект события.
     * @return void
     */
    public function handle(EventProcessed $event)
    {
        // Выбираем всех менеджеров и администраторов, кроме пользователя, который обработал событие
        $users = User::whereIn('access_level', ['admin', 'manager'])
            ->where('id', '<>', $event->user->id)
            ->get();
        // Отправляем уведомления
        Notification::send($users, new EventProcessedNotification($event->event->toArray(), $event->user->toArray()));
        // Пишем сообщение в лог
        $message = "Anonymous event #".$event->event->id." was ";
        $message .= $event->event->approved ? "approved" : "rejected";
        $message .= " by user ".$event->user->name;
        Log::channel('user_actions')->info($message);
    }
}

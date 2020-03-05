<?php

namespace App\Listeners;

use App\Components\Entities\RDsListener;
use App\Notifications\RDsAddedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Класс, представляющий слушатель события добавления ответственного подразделения к событию.
 * @package App\Listeners Классы-слушатели.
 */
class RDsAddedListener extends RDsListener
{
    /**
     * Отправляет пользователям уведомления.
     *
     * @param \Illuminate\Support\Collection|array|mixed $users список пользователей для уведомления.
     * @param array $event массив с данными о событии
     * @param array $user массив с данными о пользователе
     * @return void
     */
    public function sendNotifications($users, array $event, array $user)
    {
        // Отправляем соответствующее уведомление
        Notification::send($users, new RDsAddedNotification($event, $user));
    }
}

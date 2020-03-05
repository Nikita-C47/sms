<?php

namespace App\Listeners;

use App\Components\Entities\RDsListener;
use App\Notifications\RDsRemovedNotification;
use Illuminate\Support\Facades\Notification;

class RDsRemovedListener extends RDsListener
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
        Notification::send($users, new RDsRemovedNotification($event, $user));
    }
}

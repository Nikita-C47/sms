<?php

namespace App\Listeners;

use App\Components\Entities\RDsListener;
use App\Events\RDsAdded;
use App\Notifications\RDsAddedNotification;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class RDsAddedListener extends RDsListener
{
    /**
     * Sends notifications for users
     *
     * @param \Illuminate\Support\Collection|array|mixed $users
     * @param array $event
     * @param array $user
     * @return void
     */
    public function sendNotifications($users, array $event, array $user)
    {
        Notification::send($users, new RDsAddedNotification($event, $user));
    }
}

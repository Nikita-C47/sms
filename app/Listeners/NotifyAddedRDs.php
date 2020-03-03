<?php

namespace App\Listeners;

use App\Components\Entities\ResponsibleDepartmentsListener;
use App\Events\ResponsibleDepartmentsAdded;
use App\Notifications\RDAdded;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotifyAddedRDs extends ResponsibleDepartmentsListener
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
        Notification::send($users, new RDAdded($event, $user));
    }
}

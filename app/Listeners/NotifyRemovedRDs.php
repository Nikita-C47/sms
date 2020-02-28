<?php

namespace App\Listeners;

use App\Events\ResponsibleDepartmentRemoved;
use App\Notifications\RDRemoved;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyRemovedRDs implements ShouldQueue
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
     * Handle the event.
     *
     * @param  ResponsibleDepartmentRemoved  $event
     * @return void
     */
    public function handle(ResponsibleDepartmentRemoved $event)
    {
        /** @var User[] $users */
        $users = User::whereIn('department_id', $event->departments)->where('access_level', 'manager')->get();
        Notification::send($users, new RDRemoved($event->event));
    }
}

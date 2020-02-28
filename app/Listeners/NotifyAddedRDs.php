<?php

namespace App\Listeners;

use App\Events\ResponsibleDepartmentAdded;
use App\Notifications\RDAdded;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAddedRDs implements ShouldQueue
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
     * @param  ResponsibleDepartmentAdded  $event
     * @return void
     */
    public function handle(ResponsibleDepartmentAdded $event)
    {
        /** @var User[] $users */
        $users = User::whereIn('department_id', $event->departments)->where('access_level', 'manager')->get();
        Notification::send($users, new RDAdded($event->event));
    }
}

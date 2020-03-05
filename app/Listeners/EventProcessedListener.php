<?php

namespace App\Listeners;

use App\Events\EventProcessed;
use App\Notifications\EventProcessedNotification;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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
     * Handle the event.
     *
     * @param  EventProcessed  $event
     * @return void
     */
    public function handle(EventProcessed $event)
    {
        $users = User::whereIn('access_level', ['admin', 'manager'])
            ->where('id', '<>', $event->user->id)
            ->get();
        Notification::send($users, new EventProcessedNotification($event->event->toArray(), $event->user->toArray()));

        $message = "Anonymous event #".$event->event->id." was ";
        $message .= $event->event->approved ? "approved" : "rejected";
        $message .= " by user ".$event->user->name;

        Log::channel('user_actions')->info($message);
    }
}

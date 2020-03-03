<?php


namespace App\Components\Entities;

use App\Components\Concretes\RDsEvent;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class RDsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param RDsEvent $event
     * @return void
     */
    public function handle($event)
    {
        /** @var User[] $users */
        $users = User::whereIn('department_id', $event->departments)->where('access_level', 'manager')->get();
        $this->sendNotifications($users, $event->event->toArray(), $event->user->toArray());
    }

    /**
     * Sends notifications for users
     *
     * @param \Illuminate\Support\Collection|array|mixed $users
     * @param array $event
     * @param array $user
     * @return void
     */
    abstract public function sendNotifications($users, array $event, array $user);
}

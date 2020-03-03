<?php

namespace App\Events;

use App\Models\Events\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Event
     */
    public $event;
    /**
     * @var \App\User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Event $event
     * @param Authenticatable $user
     */
    public function __construct(Event $event, Authenticatable $user)
    {
        //
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

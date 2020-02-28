<?php

namespace App\Events;

use App\Models\Events\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResponsibleDepartmentAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $departments;
    public $event;

    /**
     * Create a new event instance.
     *
     * @param array $departments
     * @param Event $event
     */
    public function __construct(array $departments, Event $event)
    {
        $this->departments = $departments;
        $this->event = $event;
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

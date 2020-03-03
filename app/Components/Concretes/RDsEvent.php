<?php


namespace App\Components\Concretes;

use App\Models\Events\Event;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RDsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var array $departments
     */
    public $departments;
    /**
     * @var Event $event
     */
    public $event;
    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param array $departments
     * @param Event $event
     * @param Authenticatable $user
     */
    public function __construct(array $departments, Event $event, Authenticatable $user)
    {
        $this->departments = $departments;
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

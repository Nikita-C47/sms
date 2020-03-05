<?php

namespace App\Events;

use App\Models\Events\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Класс, представляющий собой событие обработки пользователем события на рейсе.
 * @package App\Events Кастомные события приложения.
 */
class EventProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /** @var Event $event событие на рейсе, которое было обработано. */
    public $event;
    /** @var \App\User пользователь, обработавший событие. */
    public $user;

    /**
     * Создает новый экземпляр класса.
     *
     * @param Event $event событие.
     * @param Authenticatable $user пользователь.
     */
    public function __construct(Event $event, Authenticatable $user)
    {
        // Инициализируем поля
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

<?php


namespace App\Components\Concretes;

use App\Models\Events\Event;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Класс, представляющий событие манипуляции с ответственными подразделениями по событию (добавление/удаление).
 * @package App\Components\Concretes Классы-основы для наследования другими классами.
 */
class RDsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**@var array $departments массив с ключами подразделений, которые были добавлены/удалены). */
    public $departments;
    /** @var Event $event событие, к которому относятся подразделения. */
    public $event;
    /** @var User пользователь, который произвел манипуляцию с событием. */
    public $user;

    /**
     * Создает новый экземпляр класса.
     *
     * @param array $departments массив с ключами подразделений.
     * @param Event $event событие.
     * @param Authenticatable $user пользователь.
     */
    public function __construct(array $departments, Event $event, Authenticatable $user)
    {
        // Устанавливаем поля
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

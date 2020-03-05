<?php


namespace App\Components\Entities;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Класс, представляющий уведомление о манипуляции с ответственными подразделениями события.
 * @package App\Components\Entities Классы-абстракции для определения сущностей с общими методами.
 */
abstract class RDsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /** @var array $event массив с данными о событии. */
    protected $event;
    /** @var array $user массив с данными о пользователе. */
    protected $user;

    /**
     * Создает новый экземпляр класса.
     *
     * @param array $event событие.
     * @param array $user пользователь.
     */
    public function __construct(array $event, array $user)
    {
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Возвращает представление уведомления в виде email-сообщения.
     *
     * @param mixed $notifiable уведомляемый объект.
     * @return \Illuminate\Notifications\Messages\MailMessage сообщение.
     */
    abstract public function toMail($notifiable);
}

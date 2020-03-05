<?php

namespace App\Components\Entities;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Класс, представляющий уведомление по событию.
 * @package App\Components\Entities Классы-абстракции для определения сущностей с общими методами.
 */
abstract class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /** @var array массив, представляющий данные о событии. */
    protected $event;
    /** @var array|null $user массив, представляющий данные о пользователе (либо null при создании анонимного события). */
    protected $user;

    /**
     * Создает новый экземпляр класса.
     *
     * @param array $event событие.
     * @param array|null $user пользователь.
     */
    public function __construct(array $event, array $user = null)
    {
        // Инициализируем поля
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
     * Возвращает представление уведомления в виде email.
     *
     * @param mixed $notifiable уведомляемый объект.
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function toMail($notifiable)
    {
        // Если событие анонимное
        if($this->event['anonymous']) {
            // Нет нужды проверять кому идет отправка - сразу отправляем письмо о событии
            return $this->eventEmail();
        }
        // Если же событие не анонимное - отправляет пользователю, создавшему его отдельное уведомление
        /** @var \App\User $notifiable */
        return $notifiable->id === $this->event['created_by'] ? $this->emailToCreator() : $this->eventEmail();
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
     * Возвращает письмо с информацией о действии над событием.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    abstract public function eventEmail();

    /**
     * Возвращает письмо для пользователя, создавшего событие.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    abstract public function emailToCreator();
}

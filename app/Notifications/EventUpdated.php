<?php

namespace App\Notifications;

use App\Models\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Событие №'.$this->event->id.' в базе данных SMS было изменено')
            ->greeting('Здравствуйте! В системе SMS Было изменено событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event->id)
            ->line('Дата: '.$this->event->date->format('d.m.Y'))
            ->line('Текст сообщения: '. $this->event->message)
            ->line('Кем изменено: '. $this->event->user_updated_by->name)
            ->action('Просмотр события', route('view-event', ['id' => $this->event->id]))
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
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
}

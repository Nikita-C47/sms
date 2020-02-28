<?php

namespace App\Notifications;

use App\Models\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCreated extends Notification implements ShouldQueue
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
        return $this->event->anonymous ? $this->anonymousEventMail() : $this->eventMail();
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

    private function eventMail()
    {
        return (new MailMessage)
            ->subject('Новое событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS появилось новое событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event->id)
            ->line('Дата: '.$this->event->date->format('d.m.Y'))
            ->line('Текст сообщения: '. $this->event->message)
            ->line('Кем создано: '. $this->event->user_created_by->name)
            ->action('Просмотр события', route('view-event', ['id' => $this->event->id]))
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }

    private function anonymousEventMail()
    {
        return (new MailMessage)
            ->subject('Новое анонимное событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS появилось новое анонимное событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event->id)
            ->line('Дата: '.$this->event->date->format('d.m.Y'))
            ->line('Текст сообщения: '. $this->event->message)
            ->action('Просмотр события', route('view-event', ['id' => $this->event->id]))
            ->line('Обращаем внимание, что данное событие является анонимным, а значит оно не будет опубликовано пока кто-либо из менеджеров событий его не одобрит.')
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

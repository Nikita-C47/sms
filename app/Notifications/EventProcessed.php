<?php

namespace App\Notifications;

use App\Models\Events\Event;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EventProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     *
     * @param Event $event
     * @param User $user
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
        /** @var \App\User $user */
        $user = Auth::user();
        $status = $this->event->approved ? 'одобрено' : 'отклонено';
        return (new MailMessage)
                    ->subject('Обработано событие в базе данных SMS')
                    ->greeting('Здравствуйте! В системе SMS было '.$status. ' событие.')
                    ->line('Детали события:')
                    ->line('Номер: '.$this->event->id)
                    ->line('Дата: '.$this->event->date->format('d.m.Y'))
                    ->line('Текст сообщения: '.$this->event->message)
                    ->line('Обработавший пользователь: '.$user->name)
                    ->action('Просмотр события', route('view-event', ['id' => $this->event->id]))
                    ->line($this->event->approved ? 'Теперь данное событие появится в общем списке под своим номером.' : 'Если событие отклонено ошибочно, свяжитесь с указанным сотрудником.')
                    ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий или администраторов, которые получают уведомления об обработке событий.')
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

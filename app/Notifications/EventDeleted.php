<?php

namespace App\Notifications;

use App\Models\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EventDeleted extends Notification implements ShouldQueue
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
        /** @var \App\User $user */
        $user = Auth::user();

        $message = (new MailMessage())
            ->subject('Удалено событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS было удалено событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event->id)
            ->line('Дата: '.$this->event->date->format('d.m.Y'))
            ->line('Текст сообщения: '.$this->event->message)
            ->line('Кем удалено: '.$this->event->user_deleted_by->name);

        if($user->access_level === 'admin') {
            $message
                ->line('Обращаем Ваше внимание, что данное событие не удалено окончательно и все еще существует в базе данных. Если Вы хотите его восстановить, перейдите по ссылке ниже:')
                ->action('Просмотр события', route('view-event', ['id' => $this->event->id]))
                ->line('Вы получили это письмо, так как находитесь в группе администраторов, которые получают уведомления об удалении событий.');
        }

        if($user->access_level === 'manager') {
            $message
                ->line('Обращаем Ваше внимание, что данное событие не удалено окончательно и все еще существует в базе данных, а значит работа по нему ещё может возобновиться')
                ->line('Вы получили это письмо, так как ваше подразделение находится в списке ответственных по данному событию.');
        }

        $message
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');

        return $message;
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

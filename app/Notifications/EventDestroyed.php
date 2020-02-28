<?php

namespace App\Notifications;

use App\Models\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EventDestroyed extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Безвозвратно удалено событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS было безвозвратно удалено событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event->id)
            ->line('Дата: '.$this->event->date->format('d.m.Y'))
            ->line('Текст сообщения: '.$this->event->message)
            ->line('Кем удалено: '.$user->name)
            ->line('Обращаем Ваше внимание, что данное событие было удалено окончательно и его уже невозможно восстановить. Оно больше не появится в базе данных SMS.')
            ->line('Вы получили это письмо, так как находитесь в группе администраторов, или Ваше подразделение в списке ответственных по данному событию. Либо же это Вы подали заявку на удаление этого события.')
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

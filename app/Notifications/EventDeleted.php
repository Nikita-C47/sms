<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class EventDeleted extends EventNotification
{
    /** @var \App\User $notifiable */
    protected $notifiable;

    public function toMail($notifiable)
    {
        $this->notifiable = $notifiable;
        return parent::toMail($notifiable);
    }

    public function emailToCreator()
    {
        return (new MailMessage())
            ->subject('Ваше событие было удалено')
            ->greeting('Здравствуйте! Событие, которое вы создавали в системе SMS было удалено.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем удалено: '.$this->user->name)
            ->line('Обращаем Ваше внимание, что данное событие не удалено окончательно, а значит работа по нему ещё может возобновиться.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }

    public function eventEmail()
    {
        $message = (new MailMessage())
            ->subject('Удалено событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS было удалено событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем удалено: '.$this->user->name);

        if($this->notifiable->access_level === 'admin') {
            $message
                ->line('Обращаем Ваше внимание, что данное событие не удалено окончательно и все еще существует в базе данных. Если Вы хотите его восстановить, перейдите по ссылке ниже:')
                ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
                ->line('Вы получили это письмо, так как находитесь в группе администраторов, которые получают уведомления об удалении событий.');
        }

        if($this->notifiable->access_level === 'manager') {
            $message
                ->line('Обращаем Ваше внимание, что данное событие не удалено окончательно и все еще существует в базе данных, а значит работа по нему ещё может возобновиться')
                ->line('Вы получили это письмо, так как ваше подразделение находится в списке ответственных по данному событию.');
        }

        $message
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');

        return $message;
    }
}

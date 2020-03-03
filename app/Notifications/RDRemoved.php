<?php

namespace App\Notifications;

use App\Components\Entities\ResponsibleDepartmentsNotification;
use Illuminate\Notifications\Messages\MailMessage;

class RDRemoved extends ResponsibleDepartmentsNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ваше подразделение исключено из ответственных за событие №'.$this->event['id'])
            ->greeting('Здравствуйте! В системе SMS Было изменено событие. Теперь ваше подразделение не является ответственным за него.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем изменено: '. $this->user['name'])
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

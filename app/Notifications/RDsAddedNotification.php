<?php

namespace App\Notifications;

use App\Components\Entities\RDsNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Класс, представляющий уведомление о добавлении ответственного подразделении по событию.
 * @package App\Notifications Уведомления приложения.
 */
class RDsAddedNotification extends RDsNotification
{
    /**
     * Возвращает представление уведомления в виде email.
     *
     * @param mixed $notifiable уведомляемый объект.
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ваше подразделение назначено ответственным за событие №'.$this->event['id'])
            ->greeting('Здравствуйте! В системе SMS Было изменено событие. Теперь ваше подразделение числится в списке ответственных по нему.')
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

<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Класс, представляющий уведомление об обновлении события.
 * @package App\Notifications Уведомления приложения.
 */
class EventUpdatedNotification extends EventNotification
{
    /**
     * Возвращает письмо с информацией о действии над событием.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function eventEmail()
    {
        return (new MailMessage)
            ->subject('Событие №'.$this->event['id'].' в базе данных SMS было изменено')
            ->greeting('Здравствуйте! В системе SMS Было изменено событие.')
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

    /**
     * Возвращает письмо для пользователя, создавшего событие.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function emailToCreator()
    {
        return (new MailMessage)
            ->subject('Ваше событие №'.$this->event['id'].' было изменено')
            ->greeting('Здравствуйте! В системе SMS Было изменено созданное Вами событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем изменено: '. $this->user['name'])
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

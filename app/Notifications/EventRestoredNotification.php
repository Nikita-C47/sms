<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;

class EventRestoredNotification extends EventNotification
{
    public function eventEmail()
    {
        return (new MailMessage())
            ->subject('Восстановлено событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS было восстановлено удаленное событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем восстановлено: '.$this->user['name'])
            ->line('Работа по данному событию была возобновлена, а значит оно снова доступно для просмотра и редактирования, а также его можно найти в списке событий под его номером.')
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Вы получили это письмо, так как находитесь в группе администраторов, или Ваше подразделение в списке ответственных по данному событию. Либо же это Вы подали заявку на удаление этого события.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }

    public function emailToCreator()
    {
        return (new MailMessage())
            ->subject('Ваше событие было восстановлено')
            ->greeting('Здравствуйте! Событие, которое вы добавили в базу данных SMS было восстановлено.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем восстановлено: '.$this->user['name'])
            ->line('Работа по данному событию была возобновлена, а значит оно снова доступно для просмотра, а также его можно найти в списке событий под его номером.')
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

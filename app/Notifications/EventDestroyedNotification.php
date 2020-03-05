<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Класс, представляющий уведомление об уничтожении события.
 * @package App\Notifications Уведомления приложения.
 */
class EventDestroyedNotification extends EventNotification
{
    /**
     * Возвращает письмо с информацией о действии над событием.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function eventEmail()
    {
        return (new MailMessage)
            ->subject('Безвозвратно удалено событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS было безвозвратно удалено событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '.$this->event['message'])
            ->line('Кем удалено: '.$this->user['name'])
            ->line('Обращаем Ваше внимание, что данное событие было удалено окончательно и его уже невозможно восстановить. Оно больше не появится в базе данных SMS.')
            ->line('Вы получили это письмо, так как находитесь в группе администраторов, или Ваше подразделение в списке ответственных по данному событию. Либо же это Вы подали заявку на удаление этого события.')
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
            ->subject('Ваше событие было безвозвратно удалено')
            ->greeting('Здравствуйте! Событие, которое вы создавали в базе данных SMS, было безвозвратно удалено.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '.$this->event['message'])
            ->line('Кем удалено: '.$this->user['name'])
            ->line('Обращаем Ваше внимание, что данное событие было удалено окончательно.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

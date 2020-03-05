<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Класс, представляющий уведомление о создании события.
 * @package App\Notifications Уведомления приложения.
 */
class EventCreatedNotification extends EventNotification
{
    /**
     * Возвращает представление уведомления в виде email.
     *
     * @param mixed $notifiable уведомляемый объект.
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function toMail($notifiable)
    {
        // Если это анонимное событие - отправляем соответствующее письмо
        if($this->event['anonymous']) {
            return $this->anonymousEventMail();
        } else {
            // Иначе для создателя события отправляем отдельное письмо
            /** @var \App\User $notifiable */
            return $notifiable->id === $this->event['created_by'] ? $this->emailToCreator() : $this->eventEmail();
        }
    }

    /**
     * Возвращает письмо для пользователя, создавшего событие.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function emailToCreator()
    {
        return (new MailMessage)
            ->subject('Ваше событие успешно добавлено!')
            ->greeting('Здравствуйте! Ваше событие успешно сохранено в базе данных SMS.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }

    /**
     * Возвращает письмо с информацией о действии над событием.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function eventEmail()
    {
        return (new MailMessage)
            ->subject('Новое событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS появилось новое событие.')
            ->line('Детали события:')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->line('Кем создано: '. $this->user['name'])
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }

    /**
     * Возвращает письмо при создании анонимного события.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function anonymousEventMail()
    {
        return (new MailMessage)
            ->subject('Новое анонимное событие в базе данных SMS')
            ->greeting('Здравствуйте! В системе SMS появилось новое анонимное событие.')
            ->line('Детали события:')
            ->line('Номер: '.$this->event['id'])
            ->line('Дата: '.$this->event['formatted_date'])
            ->line('Текст сообщения: '. $this->event['message'])
            ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
            ->line('Обращаем внимание, что данное событие является анонимным, а значит оно не будет опубликовано пока кто-либо из менеджеров событий его не одобрит.')
            ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий, которые получают уведомления о всех действиях с событиями.')
            ->line('Если данное письмо попало к Вам по ошибке, пожалуйста свяжитесь со специалистами, осуществляющими техническое обслуживание базы данных SMS.')
            ->line('Данное письмо было сгенерировано автоматически. Отвечать на него не нужно.');
    }
}

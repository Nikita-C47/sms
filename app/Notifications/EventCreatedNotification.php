<?php

namespace App\Notifications;

use App\Components\Entities\EventNotification;
use Illuminate\Notifications\Messages\MailMessage;

class EventCreatedNotification extends EventNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
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

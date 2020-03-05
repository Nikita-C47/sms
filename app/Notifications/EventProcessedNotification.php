<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Класс, представляющий уведомление об обработке события.
 * @package App\Notifications Уведомления приложения.
 */
class EventProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /** @var array $event массив с информацией о событии. */
    protected $event;
    /** @var array $user массив с информацией о пользователе. */
    protected $user;

    /**
     * Создает новый экземпляр класса.
     *
     * @param array $event событие.
     * @param array $user пользователь.
     */
    public function __construct(array $event, array $user)
    {
        // Инициализируем поля
        $this->event = $event;
        $this->user = $user;
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
     * Возвращает представление уведомления в виде email.
     *
     * @param mixed $notifiable уведомляемый объект.
     * @return \Illuminate\Notifications\Messages\MailMessage email-сообщение для отправки.
     */
    public function toMail($notifiable)
    {
        $status = $this->event['approved'] ? 'одобрено' : 'отклонено';
        return (new MailMessage)
                    ->subject('Обработано событие в базе данных SMS')
                    ->greeting('Здравствуйте! В системе SMS было '.$status. ' событие.')
                    ->line('Детали события:')
                    ->line('Номер: '.$this->event['id'])
                    ->line('Дата: '.$this->event['formatted_date'])
                    ->line('Текст сообщения: '.$this->event['message'])
                    ->line('Обработавший пользователь: '.$this->user['name'])
                    ->action('Просмотр события', route('view-event', ['id' => $this->event['id']]))
                    ->line($this->event['approved'] ? 'Теперь данное событие появится в общем списке под своим номером.' : 'Если событие отклонено ошибочно, свяжитесь с указанным сотрудником.')
                    ->line('Вы получили это письмо, так как находитесь в группе менеджеров событий или администраторов, которые получают уведомления об обработке событий.')
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

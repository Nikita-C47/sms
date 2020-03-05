<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Класс, представляющий уведомление о логине и пароле для пользователя.
 * @package App\Notifications Уведомления приложения.
 */
class UserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /** @var string $password Пароль, который нужно отправить пользователю */
    private $password;
    /** @var bool $passwordChanged Флаг того, что пароль обновлен */
    private $passwordChanged;

    /**
     * Создает новый экземпляр класса.
     *
     * @param string $password Пароль, установленный пользователю
     * @param bool $passwordChanged Флаг того, что пароль изменен
     */
    public function __construct(string $password, bool $passwordChanged = false)
    {
        $this->password = $password;
        $this->passwordChanged = $passwordChanged;
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
        return (new MailMessage)
                    ->subject($this->passwordChanged ? 'Ваши новые учетные данные в базе данных SMS' : 'Добро пожаловать в базу данных SMS')
                    ->greeting($this->passwordChanged ? 'Здравствуйте!' : 'Добро пожаловать в базу данных SMS!')
                    ->line($this->passwordChanged ? 'Ваши учетные данные в базе данных SMS были обновлены.' : 'Вы были успешно зарегистрированы в приложении "База данных SMS"!')
                    ->line('Для входа используйте следующие данные: ')
                    ->line("Email: " . $notifiable->email)
                    ->line('Пароль: ' . $this->password)
                    ->action('Войти', route('login'));
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

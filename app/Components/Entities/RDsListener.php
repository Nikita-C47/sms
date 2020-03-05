<?php


namespace App\Components\Entities;

use App\Components\Concretes\RDsEvent;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Класс, представляющий слушателя для события манипуляции с ответственными подразделениями события.
 * @package App\Components\Entities Классы-абстракции для определения сущностей с общими методами.
 */
abstract class RDsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Обрабабывает событие.
     *
     * @param RDsEvent $event событие.
     * @return void
     */
    public function handle($event)
    {
        // Получаем всех менеджеров событий указанных ответственных подразделений
        /** @var User[] $users */
        $users = User::whereIn('department_id', $event->departments)->where('access_level', 'manager')->get();
        // Отправляем уведомления для них
        $this->sendNotifications($users, $event->event->toArray(), $event->user->toArray());
    }

    /**
     * Отправляет пользователям уведомления.
     *
     * @param \Illuminate\Support\Collection|array|mixed $users список пользователей для уведомления.
     * @param array $event массив с данными о событии
     * @param array $user массив с данными о пользователе
     * @return void
     */
    abstract public function sendNotifications($users, array $event, array $user);
}

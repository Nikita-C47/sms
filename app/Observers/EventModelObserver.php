<?php

namespace App\Observers;

use App\Models\Events\Event;
use App\Notifications\{EventDeletedNotification,
    EventDestroyedNotification,
    EventRestoredNotification,
    EventCreatedNotification,
    EventUpdatedNotification};
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\{Auth, Log, Notification};

/**
 * Класс, представляющий наблюдатель за моделью события.
 * @package App\Observers Классы-наблюдатели для моделей.
 */
class EventModelObserver
{
    /**
     * Обрабатывает событие добавления модели.
     *
     * @param Event $event модель.
     * @return void
     */
    public function created(Event $event)
    {
        // Получаем список рассылки
        $users = $this->getEventMailingList($event);
        // Формируем сообщение для лога
        $message = "Event #".$event->id." was created ";
        // Если событие анонимное
        if($event->anonymous) {
            // Дописываем сообщение лога
            $message .= "anonymously";
            // Отправляем уведомления без указания пользователя
            Notification::send($users, new EventCreatedNotification($event->toArray()));
        } else {
            /** @var \App\User $user */
            $user = Auth::user();
            // Иначе - дописываем кто добавил событие
            $message .= "by user ".$user->name;
            // Отправляем уведомление
            Notification::send($users, new EventCreatedNotification($event->toArray(), $user->toArray()));
        }
        // Пишем сообщение в лог
        Log::channel('user_actions')->info($message);
    }

    /**
     * Обрабатывает событие обновления модели.
     *
     * @param Event $event модель.
     * @return void
     */
    public function updated(Event $event)
    {
        // Если указано что нужно отправить уведомление (предотарвщает отправку уведомлений при удалении и восстановлении событий)
        if($event->notify) {
            // Получаем список рассылки
            $users = $this->getEventMailingList($event);
            /** @var \App\User $user */
            $user = Auth::user();
            // Отправляем уведомления
            Notification::send($users, new EventUpdatedNotification($event->toArray(), $user->toArray()));
            // Пишем сообщение в лог
            Log::channel('user_actions')
                ->info("Event #".$event->id." was updated by user ".$user->name);
        }
    }

    /**
     * Обрабатывает событие удаления модели.
     *
     * @param Event $event модель.
     * @return void
     */
    public function deleted(Event $event)
    {
        // Если модель не уничтожается (предотвращает отправку событий удаления модели при ее уничтожении)
        if(!$event->isForceDeleting()) {
            // Отправляем уведомление об удалении события заинтересованным пользователям
            $users = $this->getRemovalMailingList($event);
            /** @var \App\User $user */
            $user = Auth::user();
            // Отправляем пользователям уведомления
            Notification::send($users, new EventDeletedNotification($event->toArray(), $user->toArray()));
            // Пишем сообщение в лог
            Log::channel('user_actions')
                ->info("Event #".$event->id." was deleted by user ".$user->name);
        }
    }

    /**
     * Обрабатывает событие восстановления модели.
     *
     * @param Event $event модель.
     * @return void
     */
    public function restored(Event $event)
    {
        // Получаем список рассылки
        $users = $this->getRemovalMailingList($event, true);
        /** @var \App\User $user */
        $user = Auth::user();
        // Отправляем пользователям уведомления
        Notification::send($users, new EventRestoredNotification($event->toArray(), $user->toArray()));
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("Event #".$event->id." was restored by user ".$user->name);
    }

    /**
     * Обрабатывает событие уничтожение модели.
     *
     * @param Event $event модель.
     * @return void
     */
    public function forceDeleted(Event $event)
    {
        // Получаем список рассылки
        $users = $this->getRemovalMailingList($event, true);
        /** @var \App\User $user */
        $user = Auth::user();
        // Отправляем пользователям уведомления
        Notification::send($users, new EventDestroyedNotification($event->toArray(), $user->toArray()));
        // Пишем сообщение в лог
        Log::channel('user_actions')
            ->info("Event #".$event->id." was destroyed by user ".$user->name);
    }

    /**
     * Возвращает список рассылки для обычных операций с моделью.
     *
     * @param Event $event модель.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getEventMailingList(Event $event)
    {
        // Получаем менеджеров событий
        $users = User::real()
            ->where('access_level', 'manager');
        // Если событие не анонимное - добавляем пользователя, создавшего его
        if(!$event->anonymous) {
            $users = $users->orWhere('id', $event->created_by);
        }
        // Возвращаем список рассылки
        return $users->get();
    }

    /**
     * Возвращает список рассылки для операций по удалению и восстановлению моделей.
     *
     * @param Event $event модель.
     * @param bool $includeDeletedBy флаг, означающий что надо включить пользователя, удалившего модель.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getRemovalMailingList(Event $event, bool $includeDeletedBy = false)
    {
        // Выбираем всех администраторов
        $users = User::real()->where('access_level', 'admin');
        // Если у события заполнены ответственные подразделения
        if(filled($event->responsible_departments)) {
            // Добавляем дополнительное сгруппированное условие
            $users->orWhere(function ($query) use ($event) {
                // Дополнительно выбираем всех менеджеров ответственных подразделений по событию
                /** @var Builder $query */
                $query
                    ->whereIn('department_id', $event->responsible_departments->modelKeys())
                    ->where('access_level', 'manager');
            });
        }
        // Исключаем текущего пользователя из рассылки (он и так в курсе что он сделал, ведь правда?)
        $users = $users->where('id', '<>', Auth::user()->getAuthIdentifier());
        // Добавляем в рассылку пользователя, удалившего событие (если нужно)
        if($includeDeletedBy) {
            $users = $users->orWhere('id', $event->deleted_by);
        }
        // Если событие не анонимное
        if(!$event->anonymous) {
            // Включаем в выборку того, кто создал событие
            $users = $users->orWhere('id', $event->created_by);
        }
        // Возвращаем выборку
        return $users->get();
    }
}

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

class EventModelObserver
{
    /**
     * Handle the event "created" event.
     *
     * @param Event  $event
     * @return void
     */
    public function created(Event $event)
    {
        $users = $this->getEventMailingList($event);
        $message = "Event #".$event->id." was created ";

        if($event->anonymous) {
            $message .= "anonymously";
            Notification::send($users, new EventCreatedNotification($event->toArray()));
        } else {
            /** @var \App\User $user */
            $user = Auth::user();
            $message .= "by user ".$user->name;
            Notification::send($users, new EventCreatedNotification($event->toArray(), $user->toArray()));
        }

        Log::channel('user_actions')->info($message);
    }

    /**
     * Handle the event "updated" event.
     *
     * @param Event  $event
     * @return void
     */
    public function updated(Event $event)
    {
        if($event->notify) {
            $users = $this->getEventMailingList($event);
            /** @var \App\User $user */
            $user = Auth::user();
            Notification::send($users, new EventUpdatedNotification($event->toArray(), $user->toArray()));
            Log::channel('user_actions')
                ->info("Event #".$event->id." was updated by user ".$user->name);
        }
    }

    /**
     * Handle the event "deleted" event.
     *
     * @param Event  $event
     * @return void
     */
    public function deleted(Event $event)
    {
        if(!$event->isForceDeleting()) {
            // Отправляем уведомление об удалении события заинтересованным пользователям
            $users = $this->getRemovalMailingList($event);
            /** @var \App\User $user */
            $user = Auth::user();
            // Отправляем пользователям уведомления
            Notification::send($users, new EventDeletedNotification($event->toArray(), $user->toArray()));
            Log::channel('user_actions')
                ->info("Event #".$event->id." was deleted by user ".$user->name);
        }
    }

    /**
     * Handle the event "restored" event.
     *
     * @param Event  $event
     * @return void
     */
    public function restored(Event $event)
    {
        $users = $this->getRemovalMailingList($event, true);
        /** @var \App\User $user */
        $user = Auth::user();
        // Отправляем пользователям уведомления
        Notification::send($users, new EventRestoredNotification($event->toArray(), $user->toArray()));

        Log::channel('user_actions')
            ->info("Event #".$event->id." was restored by user ".$user->name);
    }

    /**
     * Handle the event "force deleted" event.
     *
     * @param Event  $event
     * @return void
     */
    public function forceDeleted(Event $event)
    {
        $users = $this->getRemovalMailingList($event, true);
        /** @var \App\User $user */
        $user = Auth::user();
        // Отправляем пользователям уведомления
        Notification::send($users, new EventDestroyedNotification($event->toArray(), $user->toArray()));
        Log::channel('user_actions')
            ->info("Event #".$event->id." was destroyed by user ".$user->name);
    }

    protected function getEventMailingList(Event $event)
    {
        $users = User::real()
            ->where('access_level', 'manager');

        if(!$event->anonymous) {
            $users = $users->orWhere('id', $event->created_by);
        }

        return $users->get();
    }

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

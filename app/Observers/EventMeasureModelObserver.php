<?php

namespace App\Observers;

use App\Models\Events\EventMeasure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий наблюдатель за моделью мероприятия по событию.
 * @package App\Observers
 */
class EventMeasureModelObserver
{
    /**
     * Handle the event measure "created" event.
     *
     * @param  \App\Models\Events\EventMeasure  $eventMeasure
     * @return void
     */
    public function created(EventMeasure $eventMeasure)
    {
        //
    }

    /**
     * Handle the event measure "updated" event.
     *
     * @param  \App\Models\Events\EventMeasure  $eventMeasure
     * @return void
     */
    public function updated(EventMeasure $eventMeasure)
    {
        //
    }

    /**
     * Обрабатывает событие удаления модели.
     *
     * @param \App\Models\Events\EventMeasure $eventMeasure модель.
     * @return void
     */
    public function deleted(EventMeasure $eventMeasure)
    {
        /** @var \App\User $user */
        $user = Auth::user();
        // Пишем сообщение в лог
        $message = 'User '.$user->name.' removed measure "'.$eventMeasure->text.'" from event '.$eventMeasure->event_id;
        Log::channel('user_actions')->info($message);
    }

    /**
     * Handle the event measure "restored" event.
     *
     * @param  \App\Models\Events\EventMeasure  $eventMeasure
     * @return void
     */
    public function restored(EventMeasure $eventMeasure)
    {
        //
    }

    /**
     * Handle the event measure "force deleted" event.
     *
     * @param  \App\Models\Events\EventMeasure  $eventMeasure
     * @return void
     */
    public function forceDeleted(EventMeasure $eventMeasure)
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Models\Events\EventAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий наблюдатель за моделью вложения по событию.
 * @package App\Observers Классы-наблюдатели для моделей.
 */
class EventAttachmentModelObserver
{
    /**
     * Handle the event attachment "created" event.
     *
     * @param  \App\Models\Events\EventAttachment  $eventAttachment
     * @return void
     */
    public function created(EventAttachment $eventAttachment)
    {
        //
    }

    /**
     * Handle the event attachment "updated" event.
     *
     * @param  \App\Models\Events\EventAttachment  $eventAttachment
     * @return void
     */
    public function updated(EventAttachment $eventAttachment)
    {
        //
    }

    /**
     * Обрабатывает событие удаления модели.
     *
     * @param \App\Models\Events\EventAttachment $eventAttachment модель.
     * @return void
     */
    public function deleted(EventAttachment $eventAttachment)
    {
        /** @var \App\User $user */
        $user = Auth::user();
        // Пишем сообщение в лог
        $message = 'User '.$user->name.' removed attachment '.$eventAttachment->original_name.' from event '.$eventAttachment->event_id;
        Log::channel('user_actions')->info($message);
        // Удаляем вложение из файловой системы
        $eventAttachment->removeFromFileSystem();
    }

    /**
     * Handle the event attachment "restored" event.
     *
     * @param  \App\Models\Events\EventAttachment  $eventAttachment
     * @return void
     */
    public function restored(EventAttachment $eventAttachment)
    {
        //
    }

    /**
     * Handle the event attachment "force deleted" event.
     *
     * @param  \App\Models\Events\EventAttachment  $eventAttachment
     * @return void
     */
    public function forceDeleted(EventAttachment $eventAttachment)
    {
        //
    }
}

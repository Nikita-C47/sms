<?php


namespace App\Models\Events;

use App\Models\Shared\Dictionary;

/**
 * Класс, представляющий модель типа событий.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property Event[] $events Массив связных моделей событий, относящихся к данному типу.
 */
class EventType extends Dictionary
{
    /**
     * Связь с таблицей событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('App\Models\Events\Event', 'type_id');
    }
}

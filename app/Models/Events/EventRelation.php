<?php


namespace App\Models\Events;

use App\Models\Shared\Dictionary;

/**
 * Класс, представляющий модель мероприятия, с которым связано событие.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property Event[] $events Массив связных моделей событий, относящихся к данному типу мероприятий.
 */
class EventRelation extends Dictionary
{
    /**
     * Связь с моделью событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('App\Models\Events\Event', 'relation_id');
    }
}

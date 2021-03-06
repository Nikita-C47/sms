<?php

namespace App\Models;

use App\Models\Events\Event;
use App\Models\Events\EventCategory;
use App\Models\Shared\Dictionary;

/**
 * Класс, представляющий модель подразделения.
 * @package App\Models Модели приложения общего назначения.
 *
 * @property EventCategory[] $event_categories Массив связных моделей категорий событий.
 * @property Event[] $events Массив связных моделей событий, инициированных данным подразделением.
 * @property Event[] $events_responsible Массив связных моделей событий, за которое ответственно подразделение.
 */
class Department extends Dictionary
{
    /**
     * Связь с таблицей категорий событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function event_categories()
    {
        return $this->hasMany('App\Models\Events\EventCategory');
    }

    /**
     * Связь с таблицей событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('App\Models\Events\Event');
    }

    /**
     * Связь с таблицей событий, за которое ответственно подразделение.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function events_responsible()
    {
        return $this->hasManyThrough(
            'App\Models\Events\Event',
            'App\Models\Events\EventResponsibleDepartment',
            'department_id',
            'id',
            'id',
            'event_id'
        );
    }
}

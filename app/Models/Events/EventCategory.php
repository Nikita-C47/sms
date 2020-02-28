<?php


namespace App\Models\Events;

use App\Models\Department;
use App\Models\Shared\Dictionary;

/**
 * Класс, представляющий модель категории события
 * @package App\Models\Events Модели, связанные с событиями
 *
 * @property string $code Код категории
 * @property int|null $department_id ID подразделения, к которому относится категория
 *
 * @property Department|null $department Связная модель подразделения, к которому относится категория
 * @property Event[] $events Массив связных моделей событий, относящихся к данной категории
 */
class EventCategory extends Dictionary
{
    protected $fillable = [
        'code',
        'name',
        'department_id'
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Events\Event', 'category_id');
    }
}

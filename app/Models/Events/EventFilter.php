<?php

namespace App\Models\Events;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющий модель фильтра списка событий.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property int $id ID фильтра.
 * @property int $user_id ID пользователя, для которого установлен фильтр.
 * @property string $key Тип фильтра.
 * @property string $value Значение фильтра.
 *
 * @property User $user Связная модель пользователя, для которого установлен фильтр.
 */
class EventFilter extends Model
{
    // Полный список поддерживаемых фильтров
    const FILTERS = [
        'date_from',
        'date_to',
        'boards',
        'captains',
        'airports',
        'statuses',
        'responsible_departments',
        'users',
        'relations',
        'attachments'
    ];
    // "Одиночные" фильтры (типа ключ - значение, а не ключ - массив)
    const SINGLE_FILTERS = [
        'date_from',
        'date_to',
        'attachments'
    ];
    /** @var array $fillable заполняемые поля. */
    protected $fillable = ['user_id', 'key', 'value'];

    /**
     * Связь с таблицей пользователей.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

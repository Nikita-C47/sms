<?php


namespace App\Models\Events;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющий модель подразделения, ответственного за событие.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property int $id ID записи в БД.
 * @property int $event_id ID события, с которым связано подразделение.
 * @property int $department_id ID подразделения, ответственного за событие.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon $updated_at Дата обновления записи.
 *
 * @property Event $event Связная модель события, за которое ответственно подразделение.
 * @property Department $department Связная модель подразделения.
 */
class EventResponsibleDepartment extends Model
{
    /** @var array $fillable заполняемые поля. */
    protected $fillable = [
        'event_id',
        'department_id'
    ];

    /**
     * Свящь с таблицей событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Models\Events\Event');
    }

    /**
     * Связь с таблицей подразделений.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }
}

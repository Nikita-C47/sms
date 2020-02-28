<?php


namespace App\Models\Events;

use App\Models\Department;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющий модель подразделения, ответственного за событие
 * @package App\Models\Events Модели, связанные с событиями
 *
 * @property int $id ID записи в БД
 * @property int $event_id ID события, с которым связано подразделение
 * @property int $department_id ID подразделения, ответственного за событие
 * @property int|null $created_by ID пользователя, добавившего подразделение к событию
 * @property Carbon $created_at Дата создания записи
 * @property Carbon $updated_at Дата обновления записи
 *
 * @property Event $event Связная модель события, за которое ответственно подразделение
 * @property Department $department Связная модель подразделения
 * @property User|null $user_created_by Связная модель пользователя, добавившего запись
 */
class EventResponsibleDepartment extends Model
{
    protected $fillable = [
        'event_id',
        'department_id',
        'created_by'
    ];

    public function event()
    {
        return $this->belongsTo('App\Models\Events\Event');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function user_created_by()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}

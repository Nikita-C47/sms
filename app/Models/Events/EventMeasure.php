<?php


namespace App\Models\Events;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющий модель мероприятий, проведенных по событию.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property int $id ID записи в БД.
 * @property int $event_id ID события, с которым связано мероприятие.
 * @property string $text Текст с описанием проведенных мероприятий.
 * @property int|null $created_by ID пользователя, добавившего мероприятие.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon $updated_at Дата обновления записи.
 *
 * @property Event $event Связная модель события, к которому привязано мероприятие.
 * @property User|null $user_created_by Связная модель пользователя, добавившего запись.
 */
class EventMeasure extends Model
{
    /** @var array $fillable заполняемые поля. */
    protected $fillable = [
        'event_id',
        'text',
        'created_by'
    ];

    /**
     * Связь с таблицей событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Models\Events\Event');
    }

    /**
     * Связь с таблицей пользователей.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_created_by()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}

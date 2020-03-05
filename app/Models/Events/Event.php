<?php


namespace App\Models\Events;

use App\Models\Department;
use App\Models\Flight;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Класс, представляющий модель события.
 * @package App\Models\Events Модели, связанные с событиями.
 *
 * @property int $id ID записи в БД.
 * @property Carbon $date Дата события.
 * @property int|null $flight_id ID рейса, с которым связано событие.
 * @property int|null $department_id ID отдела, который инициировал событие.
 * @property int|null $relation_id ID мероприятия, с которым событие связано.
 * @property int|null $type_id ID типа события.
 * @property int|null $category_id ID категории события.
 * @property bool|null $approved Флаг того, что событие одобрено (для анонимных событий).
 * @property string $approval_status Текст статуса одобрения события.
 * @property string|null $initiator Инициатор события (ФИО).
 * @property string|null $airport Аэропорт, в котором произошло событие.
 * @property string $status Статус события.
 * @property string $status_text Текст статуса события.
 * @property string $status_row_class Класс статуса для таблицы событий.
 * @property string $status_badge_class Класс для бейджа со статусом события.
 * @property string $message Описание события.
 * @property string|null $commentary Комментарий к событию.
 * @property string|null $reason Причина возникновения события.
 * @property string|null $decision Решение, принятое по событию.
 * @property Carbon|null $fix_date Дата устранения события.
 * @property bool $anonymous Флаг того, что событие является анонимным.
 * @property int|null $created_by ID пользователя, добавившего событие.
 * @property int|null $updated_by ID пользователя, обновившего событие.
 * @property int|null $deleted_by ID пользователя, удалившего событие.
 * @property Carbon $created_at Дата создания события.
 * @property Carbon $updated_at Дата обновления события.
 * @property Carbon $deleted_at Дата удаления события.
 * @property bool $notify Флаг того, что нужно отправлять уведомления (для запрета отправки в определенных сценариях).
 *
 * @property Flight|null $flight Связная модель рейса, с которым связано событие.
 * @property Department|null $department Связная модель отдела, который инициировал событие.
 * @property EventRelation|null $relation Связная модель мероприятия, к которому относится событие.
 * @property EventType|null $type Связная модель типа события.
 * @property EventCategory|null $category Связная модель категории события.
 * @property User|null $user_created_by Связная модель пользователя, создавшего событие.
 * @property User|null $user_updated_by Связная модель пользователя, обновившего событие.
 * @property User|null $user_deleted_by Связная модель пользователя, удалившего событие.
 * @property EventMeasure[] $measures Массив связных моделей мероприятий, предпринятых по данному событию.
 * @property Collection $responsible_departments Массив связных моделей отделов, ответственных за данное событие.
 * @property EventAttachment[] $attachments Массив связных моделей файлов, прикрепленных к событию.
 *
 * @method static Builder approved() Показывает только одобренные события.
 * @method static Builder notApproved() Показывает только отклоненные события.
 * @method static Builder needsApproval() Показывает события, которые необходимо одобрить или отклонить.
 */
class Event extends Model
{
    use SoftDeletes;

    // Статусы события
    const EVENT_STATUSES = [
        'new' => 'Новое',
        'fixed' => 'Решено',
        'not_fixed' => 'Не решено'
    ];

    /** @var array $fillable заполняемые поля. */
    protected $fillable = [
        'date',
        'flight_id',
        'department_id',
        'relation_id',
        'type_id',
        'category_id',
        'approved',
        'initiator',
        'airport',
        'status',
        'message',
        'commentary',
        'reason',
        'decision',
        'fix_date',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    /** @var array $casts сопоставляемые атрибуты. */
    protected $casts = [
        'date' => 'datetime',
        'approved' => 'boolean',
        'fix_date' => 'datetime'
    ];
    /** @var array $appends дополнительные поля для серализации. */
    protected $appends = [
        'anonymous',
        'formatted_date'
    ];
    /** @var bool $notify флаг отправки уведомлений. */
    public $notify = true;

    /**
     * Возвращает атрибут с текстом статуса.
     *
     * @return mixed|string
     */
    public function getStatusTextAttribute()
    {
        if(array_key_exists($this->status, self::EVENT_STATUSES)) {
            return self::EVENT_STATUSES[$this->status];
        }

        return "";
    }

    /**
     * Возвращает атрибут с классом для строки в таблице событий.
     *
     * @return string
     */
    public function getStatusRowClassAttribute()
    {
        switch ($this->status) {
            case "fixed": {
                $class = "table-success";
                break;
            }
            case "not_fixed": {
                $class = "table-danger";
                break;
            }
            default: {
                $class = "";
            }
        }

        return $class;
    }

    /**
     * Возвращает атрибут с классом для бейджа статуса события.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case "fixed": {
                $class = "badge-success";
                break;
            }
            case "not_fixed": {
                $class = "badge-danger";
                break;
            }
            default: {
                $class = "badge-primary";
            }
        }

        return $class;
    }

    /**
     * Возвращает атрибут с флагом того, что событие анонимное.
     *
     * @return bool
     */
    public function getAnonymousAttribute()
    {
        return !filled($this->created_by);
    }

    /**
     * Возвращает атрибут со статусом одобрения события.
     *
     * @return string
     */
    public function getApprovalStatusAttribute()
    {
        if(filled($this->approved)) {
            return $this->approved ? "Одобрено" : "Отклонено";
        } else {
            return "Не обработано";
        }
    }

    /**
     * Возвращает атрибут с отформатированной датой (для уведомлений и прочего).
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d.m.Y');
    }

    /**
     * Связь с таблицей рейсов.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flight()
    {
        return $this->belongsTo('App\Models\Flight');
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

    /**
     * Связь с таблицей мероприятий, к которым относится событие.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo('App\Models\Events\EventRelation');
    }

    /**
     * Связь с таблицей типов событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Models\Events\EventType');
    }

    /**
     * Связь с таблицей категории событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Events\EventCategory');
    }

    /**
     * Связь с таблицей пользователей (пользователь, создавший событие).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_created_by()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    /**
     * Связь с таблицей пользователей (пользователь, обновивший событие).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_updated_by()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }

    /**
     * Связь с таблицей пользователей (пользователь, удаливший событие).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_deleted_by()
    {
        return $this->belongsTo('App\User', 'deleted_by');
    }

    /**
     * Связь с таблицей мероприятий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function measures()
    {
        return $this->hasMany('App\Models\Events\EventMeasure');
    }

    /**
     * Связь с таблицей ответственных подразделений
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function responsible_departments()
    {
        return $this->hasManyThrough(
            'App\Models\Department',
            'App\Models\Events\EventResponsibleDepartment',
            'event_id',
            'id',
            'id',
            'department_id'
        );
    }

    /**
     * Связь с таблицей вложений.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany('App\Models\Events\EventAttachment');
    }

    /**
     * Фильтр, выбирающий только одобренные события.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Фильтр, выбирающий только отклоненные события.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotApproved($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Фильтр, выбирающий только события, которые нужно обработать.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsApproval($query)
    {
        return $query->whereNull('approved');
    }
}

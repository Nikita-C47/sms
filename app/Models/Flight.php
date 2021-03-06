<?php


namespace App\Models;

use App\Models\Events\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющтий модель рейса.
 * @package App\Models Модели приложения общего назначения.
 *
 * @property int $id ID записи в БД.
 * @property Carbon $departure_datetime Дата и время вылета.
 * @property Carbon $arrival_datetime Дата и время прилета.
 * @property string $departure_date Дата вылета.
 * @property string $arrival_date Дата прилета.
 * @property string $number Номер рейса.
 * @property string $board Номер борта.
 * @property string|null $aircraft_code Код ВС.
 * @property string $departure_airport Аэропорт вылета.
 * @property string $arrival_airport Аэропорт прилета.
 * @property string $captain КВС.
 * @property string|null $extra_captain Второй КВС.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon $updated_at Дата обновления записи.
 *
 * @property Event[] $events Массив связных моделей событий, произошедших на данном рейсе.
 */
class Flight extends Model
{
    /** @var array $appends дополнительные поля для серализации. */
    protected $appends = ['departure_date', 'arrival_date'];
    /** @var array $fillable заполняемые поля. */
    protected $fillable = [
        'departure_datetime',
        'arrival_datetime',
        'number',
        'board',
        'aircraft_code',
        'departure_airport',
        'arrival_airport',
        'captain',
        'extra_captain'
    ];
    /** @var array $casts сопоставляемые атрибуты. */
    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime'
    ];

    /**
     * Возвращает атрибут отформатированной даты выллета.
     *
     * @return string
     */
    public function getDepartureDateAttribute()
    {
        return $this->departure_datetime->format('d.m.Y');
    }

    /**
     * Возвращает атрибут отформатированной даты прилета.
     *
     * @return string
     */
    public function getArrivalDateAttribute()
    {
        return $this->arrival_datetime->format('d.m.Y');
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
}

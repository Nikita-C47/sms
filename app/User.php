<?php

namespace App;

use App\Models\Department;
use App\Models\Events\Event;
use App\Models\Events\EventFilter;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Класс, представляющий модель пользователя в приложении.
 * @package App Общее пространство имен приложения.
 *
 * @property int $id ID записи в БД.
 * @property string $name ФИО пользователя.
 * @property string $email Email пользователя.
 * @property Carbon $email_verified_at Дата подтверждения email.
 * @property string $password Пароль пользователя.
 * @property string $access_level Уровень доступа.
 * @property bool $service Флаг того, что учетная запись является сервисной.
 * @property string|null $role Роль пользователя.
 * @property int|null $department_id ID подразделения, к которому относится пользователь.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon $updated_at Дата обновления записи.
 *
 * @property Event[] $created_events Массив связных моделей созданных пользователем событий.
 * @property Event[] $updated_events Массив связных моделей обновленных пользователем событий.
 * @property Department $department Связная модель подразделения, в котором состоит пользователь.
 * @property EventFilter[] $event_filters Связная модель фильтров списка событий данного пользователя.
 *
 * @method static Builder real() Показывает только реальных пользователей.
 * @method static Builder service() Показывает только сервисных пользователей.
 */
class User extends Authenticatable
{
    use Notifiable;
    /** @var array $roles Текстовые обозначения ролей пользователя */
    const USER_ROLES = [
        'user' => 'Пользователь',
        'manager' => 'Менеджер событий',
        'admin' => 'Администратор'
    ];

    /** @var array $fillable заполняемые поля. */
    protected $fillable = [
        'name', 'email', 'password', 'access_level', 'service', 'department_id', 'api_token'
    ];

    /** @var array $hidden атрибуты, скрываемые в массивах. */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /** @var array $casts сопоставляемые атрибуты. */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'service' => 'boolean'
    ];

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
     * Связь с таблицей событий (созданные события).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function created_events()
    {
        return $this->hasMany('App\Models\Events\Event', 'created_by');
    }

    /**
     * Связь с таблицей пользователей (обновленные события).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updated_events()
    {
        return $this->hasMany('App\Models\Events\Event', 'updated_by');
    }

    /**
     * Связь с таблицей фильтров списка событий.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function event_filters()
    {
        return $this->hasMany('App\Models\Events\EventFilter');
    }

    /**
     * Возвращает атрибут с названием роли пользователя.
     *
     * @return string|null
     */
    public function getRoleAttribute()
    {
        if(!array_key_exists($this->access_level, self::USER_ROLES)) {
            return null;
        }

        return self::USER_ROLES[$this->access_level];
    }

    /**
     * Проверяет, есть ли у пользователя указанная роль.
     *
     * @param string $role роль.
     * @return bool
     */
    public function hasRole(string $role)
    {
        // У администратора по-умолчанию полный доступ к приложению
        if($this->access_level === 'admin') {
            return true;
        }
        return $this->access_level === $role;
    }

    /**
     * Фильтр, выбирающий только реальные учетные записи.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReal($query)
    {
        return $query->where('service', false);
    }

    /**
     * Фильтр, выбирающий только сервисные учетные записи.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeService($query)
    {
        return $query->where('service', true);
    }
}

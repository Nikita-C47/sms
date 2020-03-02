<?php

namespace App;

use App\Models\Department;
use App\Models\Events\Event;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Класс, представляющий модель пользователя в приложении
 * @package App Общее пространство имен приложения
 *
 * @property int $id ID записи в БД
 * @property string $name ФИО пользователя
 * @property string $email Email пользователя
 * @property Carbon $email_verified_at Дата подтверждения email
 * @property string $password Пароль пользователя
 * @property string $access_level Уровень доступа
 * @property bool $service Флаг того, что учетная запись является сервисной
 * @property string|null $role Роль пользователя
 * @property int|null $department_id ID подразделения, к которому относится пользователь
 * @property Carbon $created_at Дата создания записи
 * @property Carbon $updated_at Дата обновления записи
 *
 * @property Event[] $created_events Массив связных моделей созданных пользователем событий
 * @property Event[] $updated_events Массив связных моделей обновленных пользователем событий
 * @property Department $department Связная модель подразделения, в котором состоит пользователь
 *
 * @method static Builder real() Показывает только реальных пользователей
 * @method static Builder service() Показывает только сервисных пользователей
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'access_level', 'service', 'department_id', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'service' => 'boolean'
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function created_events()
    {
        return $this->hasMany('App\Models\Events\Event', 'created_by');
    }

    public function updated_events()
    {
        return $this->hasMany('App\Models\Events\Event', 'updated_by');
    }

    /**
     * Возвращает название роли пользователя
     *
     * @return string|null Название роли пользователя
     */
    public function getRoleAttribute()
    {
        if(!array_key_exists($this->access_level, self::USER_ROLES)) {
            return null;
        }

        return self::USER_ROLES[$this->access_level];
    }

    public function hasRole(string $role)
    {
        // У администратора по-умолчанию полный доступ к приложению
        if($this->access_level === 'admin') {
            return true;
        }

        return $this->access_level === $role;
    }

    /**
     * Scope a query to only include real users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReal($query)
    {
        return $query->where('service', false);
    }

    /**
     * Scope a query to only include service users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeService($query)
    {
        return $query->where('service', true);
    }
}

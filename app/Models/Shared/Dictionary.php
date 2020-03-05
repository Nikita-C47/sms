<?php

namespace App\Models\Shared;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс, представляющий словарь пар ID => Значение.
 * @package App\Models\Shared Общие модели приложения.
 *
 * @property int $id ID записи.
 * @property string $name Название записи.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon $updated_at Дата обновления записи.
 */
class Dictionary extends Model
{
    /** @var array $fillable заполняемые поля. */
    protected $fillable = ['name'];
}

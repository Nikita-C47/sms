<?php


namespace App\Models\Events;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Класс, представляющий модель файла, прикрепленного к событию
 * @package App\Models\Events Модели, связанные с событиями
 *
 * @property int $id ID записи в БД
 * @property int $event_id ID события, с которым связано мероприятие
 * @property string $name Название файла на диске
 * @property string $original_name Оригинальное название файла
 * @property string $extension Расширение файла
 * @property int $size Размер файла
 * @property string $size_text Человекопонятное представление размера файла
 * @property string $link Ссылка на файл в публичной части
 * @property string $path Путь к файлу в хранилище
 * @property int|null $created_by ID пользователя, добавившего файл
 * @property Carbon $created_at Дата создания записи
 * @property Carbon $updated_at Дата обновления записи
 *
 * @property Event $event Событие, к которому прикреплен файл
 * @property User|null $user_created_by Связная модель пользователя, добавившего запись
 */
class EventAttachment extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'original_name',
        'extension',
        'size',
        'created_by'
    ];

    protected $appends = [
        'size_text',
        'link'
    ];

    public function saveInFilesystem(UploadedFile $file)
    {
        $fileName = Str::random();
        $file->storeAs('events/'.$this->event_id, $fileName.'.'.$file->getClientOriginalExtension(), 'public');
        $this->name = $fileName;
    }

    public function removeFromFileSystem()
    {
        Storage::disk('public')->delete($this->path);
    }

    public function getSizeTextAttribute()
    {
        $bytes = $this->size;
        $decimals = 2;
        $size = array('Б','КБ','МБ','ГБ','ТБ','ПБ','ЭБ','ЗБ','ЙБ');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
    }

    public function getLinkAttribute()
    {
        return config('app.url').'/storage/events/'.$this->event_id.'/'.$this->name.'.'.$this->extension;
    }

    public function getPathAttribute()
    {
        return 'events/'.$this->event_id.'/'.$this->name.'.'.$this->extension;
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Events\Event');
    }

    public function user_created_by()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}

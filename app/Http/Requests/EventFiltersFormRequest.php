<?php

namespace App\Http\Requests;

use App\Models\Events\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Класс, представляющий запрос на сохранение фильтров списка событий.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class EventFiltersFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Запрос может выполнять только авторизованный пользователь
        return Auth::check();
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        return [
            // Начальная дата
            'date_from' => 'nullable|date_format:d.m.Y|before_or_equal:date_to',
            // Конечная дата
            'date_to' => 'nullable|date_format:d.m.Y|after_or_equal:date_to|before_or_equal:'.now()->format('d.m.Y'),
            // Борта
            'boards' => 'nullable|array',
            'boards.*' => 'exists:flights,board',
            // КВС
            'captains' => 'nullable|array',
            'captains.*' => 'exists:flights,captain',
            // Где произошло
            'airports' => 'nullable|array',
            'airports.*' => 'exists:events,airport',
            // Статусы событий
            'statuses' => 'nullable|array',
            'statuses.*' => 'in:'.implode(",", Event::EVENT_STATUSES),
            // Ответственные подразделения
            'responsible_departments' => 'nullable|array',
            'responsible_departments.*' => 'exists:departments,id',
            // Пользователи
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            // К чему относится подразделение
            'relations' => 'nullable|array',
            'relations.*' => 'exists:event_relations,id',
            // Прикрепленные файлы
            'attachments' => 'nullable|boolean'
        ];
    }
}

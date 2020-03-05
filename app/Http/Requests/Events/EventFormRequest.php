<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Класс, представляющий запрос на добавление/обновление события.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class EventFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Если это обновление событий, его может выполнять только менеджер
        if($this->has('event_id')) {
            return Gate::allows('manager');
        }
        // Создавать событие может только авторизованный пользователь
        return Auth::check();
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        // Базовые правила валидации
        $rules = [
            // Дата события
            'date' => 'required|date_format:d.m.Y|before_or_equal:'.now()->format('d.m.Y'),
            // ID рейса, с которым связано событие
            'flight_id' => 'required_with:flight_connection|exists:flights,id',
            // ID мероприятия, к которому относится событие
            'relation_id' => 'nullable|exists:event_relations,id',
            // ID подразделения
            'department_id' => 'nullable|exists:departments,id',
            // ID категории события
            'category_id' => 'nullable|exists:event_categories,id',
            // ID типа события
            'type_id' => 'nullable|exists:event_types,id',
            // Вложения
            'attachments' => 'array',
            'attachments.*' => 'file',
            // Сообщение
            'message' => 'required',
            // Статус события
            'status' => 'required|in:new,fixed,not_fixed'
        ];

        // Если это обновление события - добавляем еще правила
        if($this->has('event_id')) {
            // Статус одобрения
            $rules['approved'] = 'nullable|boolean';
            // дата устранения
            $rules['fix_date'] = 'nullable|date_format:d.m.Y|before_or_equal:'.now()->format('d.m.Y') . '|after_or_equal:'.$this->get('date');
            // Ответственные подразделения
            $rules['responsible_departments'] = 'array';
            $rules['responsible_departments.*'] = 'exists:departments,id';
            // Формируем правила для мероприятий
            for($i = 0; $i < $this->get('measures_count'); $i++) {
                $rules['measure_'.$i] = 'required';
            }
            // Формируем правила для удаленных мероприятий
            for($i = 0; $i < $this->get('removed_measures_count'); $i++) {
                $rules['removed_measure_'.$i] = 'required|exists:event_measures,id';
            }
            // Формируем правила для удаленных вложений
            for($i = 0; $i < $this->get('removed_attachments_count'); $i++) {
                $rules['removed_attachment_'.$i] = 'required|exists:event_attachments,id';
            }
        }
        // Возвращаем массив с правилами
        return $rules;
    }
}

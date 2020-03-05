<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Класс, представляющий запрос на добавление/обновление категории событий.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class EventCategoryFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Выполнять запрос может только менеджер событий
        return Gate::allows('manager');
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        $rules = [
            // Код категории
            'code' => 'required|unique:event_categories,code',
            // Название категории
            'name' => 'required'
        ];
        // Если это администратор - добавляем проверку отдела (менеджеру отдел проставится автоматически)
        if(Gate::allows('admin')) {
            $rules['department_id'] = 'required|exists:departments,id';
        }
        // Если это запрос на обновление
        if($this->has('category_id')) {
            // Игнорируем ID категории (чтобы не работало правило unique на текущую категорию)
            $rules['code'] = [
                'required',
                Rule::unique('event_categories')->ignore($this->get('category_id'))
            ];
        }
        // Возвращаем массив
        return $rules;
    }
}

<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Класс, представляющий запрос на получение списка категорий событий для подразделения.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class EventCategoriesFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Только авторизованные пользователи могут выполнять запрос
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
            // ID подразделения
            'department_id' => 'required|exists:departments,id'
        ];
    }
}

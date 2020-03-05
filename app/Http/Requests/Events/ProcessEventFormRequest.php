<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Класс, представляющий запрос на обработку события.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class ProcessEventFormRequest extends FormRequest
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
        return [
            // Статус одобрения события
            'approved' => 'required|boolean'
        ];
    }
}

<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Класс, представляющий запрос на получение списка рейсов для указанной даты.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class FlightsFormRequest extends FormRequest
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
            // Дата, на которую ищем рейсы
            'date' => 'required|date_format:Y-m-d'
        ];
    }
}

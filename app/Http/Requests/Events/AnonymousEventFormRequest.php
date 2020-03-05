<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Класс, представляющий запрос на добавление анонимного события.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class AnonymousEventFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Анонимное событие добавляют только не авторизованные пользователи
        return !Auth::check();
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        return [
            // Дата события
            'date' => 'required|date_format:d.m.Y|before_or_equal:'.now()->format('d.m.Y'),
            // Сообщение
            'message' => 'required'
        ];
    }
}

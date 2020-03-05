<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Класс, представляющий запрос на добавление/обновление элемента справочника.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class DictionaryFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Выполнять запрос может только администратор
        return Gate::allows('admin');
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        return [
            // Название элемента
            'name' => 'required'
        ];
    }
}

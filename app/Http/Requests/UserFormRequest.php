<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Класс, представляющий запрос на добавление/обновление пользователя.
 * @package App\Http\Requests\Events Запросы, связанные с событиями.
 */
class UserFormRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        // Запрос может выполнять только администратор
        return Gate::allows('admin');
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        // Формируем массив правил
        $rules = [
            // Имя пользователя
            'name' => 'required',
            // Email
            'email' => 'required|email|unique:users,email',
            // Уровень доступа
            'access_level' => 'required|in:'.implode(',', array_keys(User::USER_ROLES)),
            // Отдел
            'department_id' => 'nullable|exists:departments,id'
        ];

        // Если это обновление пользователя
        if($this->has('user_id')) {
            // Исключаем ID пользователя из правила unique
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($this->get('user_id'))
            ];
        }
        // Возвращаем массив
        return $rules;
    }
}

<?php


namespace App\Components\Entities;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Абстрактный класс, представляющий запрос к API.
 * @package App\Components\Entities Классы-абстракции для определения сущностей с общими методами.
 */
abstract class ApiRequest extends FormRequest
{
    /**
     * Определяет, авторизован ли пользователь для выполнения данного запроса.
     *
     * @return bool флаг того, что запрос можно выполнять.
     */
    public function authorize()
    {
        // Если пользователь не авторизован - сразу отдаем false
        if(!Auth::check()) {
            return false;
        }
        /** @var \App\User $user */
        $user = Auth::user();
        // Выполнять запрос может только пользователь API
        return $user->email === config('auth.api_user');
    }

    /**
     * Возвращает массив с правилами валидации для конкретного запроса.
     *
     * @return array массив правил валидации.
     */
    abstract public function rules();
}

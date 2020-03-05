<?php

namespace App\Components\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Трейт с функциями-помощниками для работы с паролями.
 * @package App\Components\Helpers Классы-помощники.
 */
trait PasswordHelper
{
    /**
     * Генерирует пароль для пользователя.
     *
     * @return string пароль.
     */
    public function generatePassword()
    {
        // Пароль генерируем только на продакшене. На тестовых окружениях генерируем стандартный пароль.
        return App::environment('production') ? Str::random(16) : "qwerty123";
    }
}

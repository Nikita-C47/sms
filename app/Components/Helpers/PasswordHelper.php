<?php


namespace App\Components\Helpers;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Трейт с функциями-помощниками для работы с паролями
 * @package App\Components\Helpers Классы-помощники для приложения
 */
trait PasswordHelper
{
    public function generatePassword()
    {
        return App::environment('production') ? Str::random(16) : "qwerty123";
    }
}

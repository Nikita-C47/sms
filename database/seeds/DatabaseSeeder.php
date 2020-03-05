<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

/**
 * Класс, представляющий сидер данных приложения.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Заполняет базу данных приложения.
     *
     * @return void
     */
    public function run()
    {
        // Заполняем справочники
        $this->call(DictionariesSeeder::class);
        // Если это не продакшн
        if(!App::environment('production')) {
            // Заполняем пользователей
            $this->call(UsersSeeder::class);
            // Заполняем рейсы
            $this->call(FlightsSeeder::class);
        }
    }
}

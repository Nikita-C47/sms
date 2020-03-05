<?php

use App\Models\Department;
use App\Notifications\UserCredentialsNotification;
use App\User;
use App\Components\Helpers\PasswordHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Класс, представляющий сидер справочников приложения.
 */
class UsersSeeder extends Seeder
{
    use PasswordHelper;
    /**
     * Запускает заполнение базы данных.
     *
     * @return void
     */
    public function run()
    {
        // Получаем список отделов
        $departments = Department::all();
        // Для каждого отдела
        foreach ($departments as $department) {
            // Создаем четырех пользователей
            factory(User::class, 4)->create([
                'department_id' => $department->id,
                'access_level' => 'user'
            ]);
            // Создем четырех менеджеров событий
            factory(User::class, 4)->create([
                'department_id' => $department->id,
                'access_level' => 'manager'
            ]);
            // Создаем двух администраторов
            factory(User::class, 2)->create([
                'department_id' => $department->id,
                'access_level' => 'admin'
            ]);
        }
    }
}

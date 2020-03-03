<?php

use App\Models\Department;
use App\Notifications\UserCredentialsNotification;
use App\User;
use App\Components\Helpers\PasswordHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    use PasswordHelper;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::all();

        foreach ($departments as $department) {
            factory(User::class, 4)->create([
                'department_id' => $department->id,
                'access_level' => 'user'
            ]);

            factory(User::class, 4)->create([
                'department_id' => $department->id,
                'access_level' => 'manager'
            ]);

            factory(User::class, 2)->create([
                'department_id' => $department->id,
                'access_level' => 'admin'
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Notifications\UserCredentials;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Components\Helpers\PasswordHelper;

class AdminAssign extends Command
{
    use PasswordHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns user with specified email as application admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');

        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        );

        if($validator->fails()) {
            $this->error("You must specify real email after command signature. For example - admin:create admin@airline.com");
            return 1;
        } else {
            if($this->confirm("Do you really wish to assign user with email $email as admin?")) {
                // Генерируем пароль
                $password = $this->generatePassword();
                // Создаем пользователя
                $newUser = new User([
                    'name' => 'Администратор',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'department_id' => null,
                    'access_level' => 'admin'
                ]);
                $newUser->save();
                $this->info("Embedded admin successfully created! Embedded admin password: $password");
            }
            return 0;
        }
    }
}

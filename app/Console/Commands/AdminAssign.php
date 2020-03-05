<?php

namespace App\Console\Commands;

use App\Notifications\UserCredentialsNotification;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Components\Helpers\PasswordHelper;

/**
 * Класс, представляющий консольную команду назначения администратора по-умолчанию.
 * @package App\Console\Commands Консольные команды приложения.
 */
class AdminAssign extends Command
{
    use PasswordHelper;
    /**
     * Название и сигнатура команды.
     *
     * @var string
     */
    protected $signature = 'admin:assign {email}';

    /**
     * Описание консольной команды.
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
     * Выполняет консольную команду.
     *
     * @return mixed
     */
    public function handle()
    {
        // Получаем email администратора
        $email = $this->argument('email');
        // Собираем валидатор для него
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        );
        // Если email не прошел валидацию - информируем об этом
        if($validator->fails()) {
            $this->error("You must specify real email after command signature. For example - admin:create admin@airline.com");
            return 1;
        } else {
            // Иначе - спрашиваем подтверждение
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
                // Выдаем информацию в консоль
                $this->info("Embedded admin successfully created! Embedded admin password: $password");
                // Спрашиваем нужно ли отправить учетные данные на указанный email
                if($this->confirm("Do you want to send notification with credentials on specified email?")) {
                    // Если да - отправляем письмо
                    $newUser->notify(new UserCredentialsNotification($password));
                }
            }
            return 0;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Класс, представляющий консольную команду генерации api-пользователя для приложения.
 * @package App\Console\Commands Консольные команды приложения.
 */
class ApiUserGenerate extends Command
{
    /**
     *  Название и сигнатура команды.
     *
     * @var string
     */
    protected $signature = 'api:generate';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Generates api user for application and sets api token';

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
        // Генерируем токен
        $token = Str::random(32);
        // Выдаем в консоль информацию
        $this->info("Checking if api user exists...");
        // Проверяем, существует ли пользователь для api
        $user = User::service()->where('email', config('auth.api_user'))->first();
        // Если да
        if(filled($user)) {
            // Выдаем инфо
            $this->info('Api user founded. Updating token...');
            // Обновляем его токен
            $user->fill([
                'api_token' => hash('sha256', $token)
            ]);
            // Сохраняем
            $user->save();
        } else {
            // Если пользователя нет - создаем его
            $user = new User([
                'email' => config('auth.api_user'),
                'name' => 'Пользователь API',
                'password' => Hash::make(Str::random()),
                'email_verified_at' => now(),
                'service' => true,
                'api_token' => hash('sha256', $token)
            ]);
            // Выдаем предупреждение о том, что пользователь был создан
            $this->warn('Api user is not found. Api user was generated for application.');
        }
        // Пишем в консоли инфо о пользователе api
        $this->info("Api token for user is $token. User ID: ".$user->id);

        return 0;
    }
}

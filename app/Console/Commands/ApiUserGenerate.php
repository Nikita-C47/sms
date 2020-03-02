<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiUserGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate';

    /**
     * The console command description.
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = Str::random(32);

        $this->info("Checking if api user exists...");
        $user = User::service()->where('email', config('auth.api_user'))->first();

        if(filled($user)) {
            $this->info('Api user founded. Updating token...');
            $user->fill([
                'api_token' => hash('sha256', $token)
            ]);
            $user->save();
        } else {
            $this->warn('Api user is not found. Api user was generated for application.');
            $user = new User([
                'email' => config('auth.api_user'),
                'name' => 'Пользователь API',
                'password' => Hash::make(Str::random()),
                'email_verified_at' => now(),
                'service' => true,
                'api_token' => hash('sha256', $token)
            ]);
        }

        $this->info("Api token for user is $token. User ID: ".$user->id);

        return 0;
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Шлюз для проверки доступа администратора
        Gate::define('admin' , function ($user) {
            /** @var \App\User $user */
            return $user->hasRole('admin');
        });
        // Шлюз для проверки доступа менеджера событий
        Gate::define('manager' , function ($user) {
            /** @var \App\User $user */
            return $user->hasRole('manager');
        });
        // Шлюз для проверки доступа на просмотр события
        Gate::define('view-event', function ($user, $event) {
            /** @var \App\User $user */
            /** @var \App\Models\Events\Event $event */
            if($event->trashed()) {
                // Если событие удалено - его может просматривать только администратор
                return $user->hasRole('admin');
            }
            // Если событие не удалено - его может просматривать только менеджер событий или пользователь, создавший его
            return $user->hasRole('manager') || $user->id === $event->created_by;
        });
        // Шлюз для проверки доступа к категориям событий
        Gate::define('event-category', function ($user, $eventCategory) {
            /** @var \App\User $user */
            /** @var \App\Models\Events\EventCategory $eventCategory */
            if($user->hasRole('admin')) {
                // У администраторов права на все категории
                return true;
            }
            // Если это не администратор - проверяем отдел
            return $user->department_id === $eventCategory->department_id;
        });
    }
}

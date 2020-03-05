<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

/**
 * Класс, представляющий middleware для проверки роли пользователя.
 * @package App\Http\Middleware Middleware для приложения.
 */
class RoleMiddleware
{
    /**
     * Обрабатывает запрос.
     *
     * @param \Illuminate\Http\Request $request запрос.
     * @param \Closure $next следующтй шаг.
     * @param string $role роль для проверки.
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        /** @var User $user */
        $user = $request->user();
        // Если у пользователя нет указанной роли - выбрасываем 403
        if(!$user->hasRole($role)) {
            abort(403, "Недостаточно прав для выполнения указанного действия");
        }
        // Иначе - переходим к следующему запросу
        return $next($request);
    }
}

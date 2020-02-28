<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        /** @var User $user */
        $user = $request->user();


        if(!$user->hasRole($role)) {
            abort(403, "Недостаточно прав для выполнения указанного действия");
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Проверяем, авторизован ли пользователь
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Если роли не указаны, просто пропускаем
        if (empty($roles)) {
            return $next($request);
        }

        // Проверяем, есть ли у пользователя одна из разрешённых ролей
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Если дошли до сюда - доступа нет
        abort(403, 'У вас нет доступа к этой странице.');
    }
}

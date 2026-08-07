<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, включён ли режим техработ
        if (env('MAINTENANCE_MODE', false) === true) {
            // Разрешаем доступ для указанных IP
            $allowedIps = array_map('trim', explode(',', env('MAINTENANCE_ALLOW_IPS', '')));
            if (!in_array($request->ip(), $allowedIps)) {
                // Показываем страницу техработ (HTTP 503)
                return response()->view('maintenance', [], 503);
            }
        }

        return $next($request);
    }
}

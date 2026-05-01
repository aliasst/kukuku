<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Если уже авторизован и есть права админа - в админку
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Если авторизован, но не админ - разлогиниваем
        if (Auth::check()) {
            Auth::logout();
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                // Используем именованный маршрут для перенаправления
                return redirect()->intended(route('admin.dashboard'));
            }

            // Если не админ - разлогиниваем и показываем ошибку
            Auth::logout();
            return back()->withErrors([
                'email' => 'У вас нет доступа к административной панели.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

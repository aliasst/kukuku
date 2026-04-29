<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // Показать форму запроса на сброс пароля
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Отправить ссылку на сброс пароля
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Пользователь с таким email не найден.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Генерируем токен
        $token = $user->generatePasswordResetToken();

        // Сохраняем токен в таблицу password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
            ]
        );

        // Отправляем email
        $this->sendResetEmail($user->email, $token);

        return back()->with('success', 'Ссылка на сброс пароля отправлена на ваш email.');
    }

    // Отправка email
    private function sendResetEmail($email, $token)
    {
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        Mail::send('auth.emails.password_reset', ['resetUrl' => $resetUrl, 'email' => $email], function ($message) use ($email) {
            $message->to($email)
                ->subject('Сброс пароля');
        });
    }

    // Показать форму сброса пароля
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }


    // Сбросить пароль
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        // Проверяем токен
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Недействительная ссылка для сброса пароля.']);
        }

        // Проверяем не истек ли токен (например, через 60 минут)
        $createdAt = Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Срок действия ссылки истек. Запросите сброс пароля заново.']);
        }

        // Обновляем пароль пользователя
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Удаляем токен
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Опционально: автоматически авторизуем пользователя после сброса
        // auth()->login($user);

        return redirect()->route('login')->with('success', 'Пароль успешно изменен. Теперь вы можете войти.');
    }
}

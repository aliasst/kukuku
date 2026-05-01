<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\GiftUser;
use App\Models\Wink;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Основная статистика
        $stats = [
            'users' => User::count(),
            'events' => Event::count(),
            'upcoming_events' => Event::where('type', 'upcoming')->count(),
            'past_events' => Event::where('type', 'past')->count(),
            'gifts_sent' => GiftUser::count(),
            'gifts_accepted' => GiftUser::where('status', 'accepted')->count(),
            'winks_sent' => Wink::count(),
            'winks_accepted' => Wink::where('status', 'accepted')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'editors' => User::where('role', 'editor')->count(),
            'regular_users' => User::where('role', 'user')->count(),
        ];

        // Статистика по дням (регистрация пользователей за последние 30 дней)
        $userRegistrations = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Статистика по событиям за последние 6 месяцев
        $eventsByMonth = Event::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Статистика по подаркам за последние 30 дней
        $giftsByDay = GiftUser::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Статистика по подмигиваниям за последние 30 дней
        $winksByDay = Wink::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Данные для круговой диаграммы (роли пользователей)
        $userRoles = [
            'Администраторы' => User::where('role', 'admin')->count(),
            'Редакторы' => User::where('role', 'editor')->count(),
            'Пользователи' => User::where('role', 'user')->count(),
        ];

        // Данные для круговой диаграммы (статусы подарков)
        $giftStatuses = [
            'Получены' => GiftUser::where('status', 'accepted')->count(),
            'Ожидают' => GiftUser::where('status', 'pending')->count(),
            'Отклонены' => GiftUser::where('status', 'ignored')->count(),
        ];

        // Топ блогеров (кто получил больше всего подарков)
        $topUsers = User::select('users.id', 'users.name', DB::raw('count(gift_user.id) as gifts_count'))
            ->leftJoin('gift_user', 'users.id', '=', 'gift_user.to_user_id')
            ->where('gift_user.status', 'accepted')
            ->groupBy('users.id', 'users.name')
            ->orderBy('gifts_count', 'desc')
            ->limit(5)
            ->get();

        // Последние действия (активность)
        $recentActivities = collect()
            ->merge(
                User::latest()->take(5)->get()->map(function($user) {
                    return (object) [
                        'type' => 'user',
                        'message' => "Новый пользователь: {$user->name}",
                        'time' => $user->created_at,
                        'icon' => 'fas fa-user-plus',
                        'color' => 'success'
                    ];
                })
            )
            ->merge(
                Event::latest()->take(5)->get()->map(function($event) {
                    return (object) [
                        'type' => 'event',
                        'message' => "Новое событие: {$event->title}",
                        'time' => $event->created_at,
                        'icon' => 'fas fa-calendar-plus',
                        'color' => 'primary'
                    ];
                })
            )
            ->merge(
                GiftUser::latest()->take(5)->get()->map(function($gift) {
                    return (object) [
                        'type' => 'gift',
                        'message' => "Подарок от {$gift->fromUser->name} для {$gift->toUser->name}",
                        'time' => $gift->created_at,
                        'icon' => 'fas fa-gift',
                        'color' => 'warning'
                    ];
                })
            )
            ->sortByDesc('time')
            ->take(10);

        // Подготовка данных для графиков (JSON)
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('d.m');
        }

        $userData = [];
        $giftData = [];
        $winkData = [];

        foreach ($dates as $index => $date) {
            $fullDate = now()->subDays(29 - $index)->format('Y-m-d');
            $userData[] = $userRegistrations[$fullDate] ?? 0;
            $giftData[] = $giftsByDay[$fullDate] ?? 0;
            $winkData[] = $winksByDay[$fullDate] ?? 0;
        }

        return view('admin.dashboard', compact(
            'stats',
            'dates',
            'userData',
            'giftData',
            'winkData',
            'eventsByMonth',
            'userRoles',
            'giftStatuses',
            'topUsers',
            'recentActivities'
        ));
    }
}

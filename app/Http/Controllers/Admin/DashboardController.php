<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\GiftUser;
use App\Models\Wink;

class DashboardController extends Controller
{

    public function index()
    {
        $stats = [
            'users' => User::count(),
            'events' => Event::count(),
            'upcoming_events' => Event::where('type', 'upcoming')->count(),
            'past_events' => Event::where('type', 'past')->count(),
            'gifts_sent' => GiftUser::count(),
            'gifts_received' => GiftUser::where('to_user_id', '!=', auth()->id())->count(),
            'winks_sent' => Wink::count(),
            'winks_received' => Wink::where('to_user_id', '!=', auth()->id())->count(),
            'registered_users' => User::where('role', 'user')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'editors' => User::where('role', 'editor')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentEvents = Event::latest()->take(5)->get();
        $recentGifts = GiftUser::with(['gift', 'fromUser', 'toUser'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentEvents', 'recentGifts'));
    }
}

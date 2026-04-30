<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\GiftUser;
use Illuminate\Http\Request;

class GiftController extends Controller
{


    public function index()
    {
        $gifts = Gift::orderBy('sort_order')->get();
        $giftStats = [
            'total_sent' => GiftUser::count(),
            'total_accepted' => GiftUser::where('status', 'accepted')->count(),
            'total_pending' => GiftUser::where('status', 'pending')->count(),
            'total_ignored' => GiftUser::where('status', 'ignored')->count(),
        ];
        $recentGifts = GiftUser::with(['gift', 'fromUser', 'toUser', 'event'])
            ->latest()
            ->paginate(20);

        return view('admin.gifts.index', compact('gifts', 'giftStats', 'recentGifts'));
    }

    public function updateSort(Request $request)
    {
        foreach ($request->orders as $order) {
            Gift::where('id', $order['id'])->update(['sort_order' => $order['position']]);
        }
        return response()->json(['success' => true]);
    }
}

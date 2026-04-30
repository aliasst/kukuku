<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wink;
use Illuminate\Http\Request;

class WinkController extends Controller
{


    public function index()
    {
        $stats = [
            'total' => Wink::count(),
            'accepted' => Wink::where('status', 'accepted')->count(),
            'pending' => Wink::where('status', 'pending')->count(),
            'ignored' => Wink::where('status', 'ignored')->count(),
        ];

        $winks = Wink::with(['fromUser', 'toUser', 'event'])
            ->latest()
            ->paginate(30);

        return view('admin.winks.index', compact('stats', 'winks'));
    }
}

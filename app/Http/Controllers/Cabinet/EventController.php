<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class EventController extends Controller
{

    // Страница с БУДУЩИМИ событиями
    public function upcoming(Request $request)
    {
        $query = Event::where('type', Event::TYPE_UPCOMING)
            ->where('status', '!=', 'cancelled');

        // Поиск
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Фильтр по цене
        if ($request->has('price_type')) {
            if ($request->price_type == 'free') {
                $query->where('is_free', true);
            } elseif ($request->price_type == 'paid') {
                $query->where('is_free', false)->where('price', '>', 0);
            }
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(12);

        // Статистика для фильтров
        $stats = [
            'total' => Event::where('type', Event::TYPE_UPCOMING)->count(),
            'free' => Event::where('type', Event::TYPE_UPCOMING)->where('is_free', true)->count(),
            'paid' => Event::where('type', Event::TYPE_UPCOMING)->where('is_free', false)->where('price', '>', 0)->count(),
        ];

        return view('cabinet.events.upcoming', compact('events', 'stats'));
    }

    // Страница с ПРОШЕДШИМИ событиями
    public function past(Request $request)
    {
        $query = Event::where('type', Event::TYPE_PAST);

        // Поиск
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Фильтр по цене
        if ($request->has('price_type')) {
            if ($request->price_type == 'free') {
                $query->where('is_free', true);
            } elseif ($request->price_type == 'paid') {
                $query->where('is_free', false)->where('price', '>', 0);
            }
        }

        $events = $query->orderBy('event_date', 'desc')->paginate(12);

        // Статистика для фильтров
        $stats = [
            'total' => Event::where('type', Event::TYPE_PAST)->count(),
            'free' => Event::where('type', Event::TYPE_PAST)->where('is_free', true)->count(),
            'paid' => Event::where('type', Event::TYPE_PAST)->where('is_free', false)->where('price', '>', 0)->count(),
        ];

        return view('cabinet.events.past', compact('events', 'stats'));
    }





    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_online' => 'boolean',
            'online_link' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['price'] = $request->price ?? 0;
        $data['is_free'] = $data['price'] == 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Событие успешно создано!');
    }

    // Просмотр одного события
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Похожие события (такого же типа)
        $relatedEvents = Event::where('id', '!=', $event->id)
            ->where('type', $event->type)
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_online' => 'boolean',
            'online_link' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_free'] = ($request->price ?? 0) == 0;

        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        $event->update($data);

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Событие обновлено!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Событие удалено');
    }

    // Получить участников события для модального окна
    public function getParticipants($id)
    {
        $event = Event::findOrFail($id);

        $participants = $event->users()
            ->wherePivot('status', 'attended')
            ->with('profile')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->profile && $user->profile->avatar
                        ? Storage::url($user->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=667eea&color=fff&size=80'
                ];
            });

        return response()->json([
            'success' => true,
            'participants' => $participants
        ]);
    }
}

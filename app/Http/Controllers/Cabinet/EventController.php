<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GiftUser;
use App\Models\Gift;
use App\Models\Wink;
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
        $gifts = Gift::where('is_active', true)->orderBy('sort_order')->get();

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

        return view('cabinet.events.past', compact('events', 'gifts', 'stats'));
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
                        : '/storage/img/ava.png'
                ];
            });

        return response()->json([
            'success' => true,
            'participants' => $participants
        ]);
    }

    // Сохранить подарок (AJAX)
    public function sendGift(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'gift_id' => 'required|exists:gifts,id',
            'to_user_ids' => 'required|array',
            'to_user_ids.*' => 'exists:users,id',
            'message' => 'nullable|string|max:500'
        ]);

        $fromUserId = auth()->id();
        $eventId = $request->event_id;
        $giftId = $request->gift_id;
        $message = $request->message;

        $saved = 0;
        $errors = [];

        foreach ($request->to_user_ids as $toUserId) {
            try {
                GiftUser::create([
                    'gift_id' => $giftId,
                    'from_user_id' => $fromUserId,
                    'to_user_id' => $toUserId,
                    'event_id' => $eventId,
                    'message' => $message
                ]);
                $saved++;
            } catch (\Exception $e) {
                $errors[] = "Ошибка при отправке пользователю ID: {$toUserId}";
            }
        }

        if ($saved > 0) {
            return response()->json([
                'success' => true,
                'message' => "Подарок успешно отправлен {$saved} пользователям!",
                'sent_count' => $saved,
                'errors' => $errors
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось отправить подарок',
            'errors' => $errors
        ], 500);
    }


    // Отправить подмигивание
    public function sendWink(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'to_user_ids' => 'required|array',
            'to_user_ids.*' => 'exists:users,id',
        ]);

        $fromUserId = auth()->id();
        $eventId = $request->event_id;

        $toUserIds = array_filter($request->to_user_ids, function($id) use ($fromUserId) {
            return $id != $fromUserId;
        });

        if (empty($toUserIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя подмигнуть самому себе'
            ], 400);
        }

        $saved = 0;
        $mutualCount = 0;
        $errors = [];

        foreach ($toUserIds as $toUserId) {
            try {
                $exists = Wink::where('from_user_id', $fromUserId)
                    ->where('to_user_id', $toUserId)
                    ->where('event_id', $eventId)
                    ->exists();

                if (!$exists) {
                    $wink = Wink::create([
                        'from_user_id' => $fromUserId,
                        'to_user_id' => $toUserId,
                        'event_id' => $eventId,
                    ]);
                    $saved++;

                    // Проверяем встречное подмигивание
                    $mutualWink = Wink::where('from_user_id', $toUserId)
                        ->where('to_user_id', $fromUserId)
                        ->where('event_id', $eventId)
                        ->where('status', Wink::STATUS_PENDING)
                        ->first();

                    if ($mutualWink) {
                        $mutualWink->status = Wink::STATUS_ACCEPTED;
                        $mutualWink->save();

                        $wink->status = Wink::STATUS_ACCEPTED;
                        $wink->save();

                        $mutualCount++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Ошибка при отправке пользователю ID: {$toUserId}";
            }
        }

        $message = "😉 Подмигивание отправлено {$saved} пользователям!";
        if ($mutualCount > 0) {
            $message .= " У вас взаимная симпатия с {$mutualCount} пользователями! 💕";
        }

        if ($saved > 0) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $saved,
                'mutual_count' => $mutualCount,
                'errors' => $errors
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось отправить подмигивание',
            'errors' => $errors
        ], 500);
    }

// Подтвердить подмигивание
    public function acceptWink($winkId)
    {
        $wink = Wink::findOrFail($winkId);

        if ($wink->to_user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Доступ запрещен'], 403);
        }

        $wink->status = Wink::STATUS_ACCEPTED;
        $wink->save();

        // Проверяем встречное подмигивание
        $mutualWink = Wink::where('from_user_id', $wink->to_user_id)
            ->where('to_user_id', $wink->from_user_id)
            ->where('event_id', $wink->event_id)
            ->first();

        if ($mutualWink && $mutualWink->status === Wink::STATUS_PENDING) {
            $mutualWink->status = Wink::STATUS_ACCEPTED;
            $mutualWink->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Подмигивание подтверждено! 💕'
        ]);
    }

// Проигнорировать подмигивание
    public function ignoreWink($winkId)
    {
        $wink = Wink::findOrFail($winkId);

        if ($wink->to_user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Доступ запрещен'], 403);
        }

        $wink->status = Wink::STATUS_IGNORED;
        $wink->save();

        return response()->json([
            'success' => true,
            'message' => 'Подмигивание проигнорировано'
        ]);
    }

// Получить список подмигиваний
    public function getMyWinks()
    {
        $receivedWinks = Wink::where('to_user_id', auth()->id())
            ->with(['fromUser', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();

        $sentWinks = Wink::where('from_user_id', auth()->id())
            ->with(['toUser', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cabinet.winks.index', compact('receivedWinks', 'sentWinks'));
    }

// Получить количество непросмотренных
    public function getUnviewedWinksCount()
    {
        return response()->json([
            'count' => Wink::getUnviewedCount(auth()->id())
        ]);
    }



// Получить подарок (подтвердить)
    public function acceptGift($giftId)
    {
        $gift = GiftUser::findOrFail($giftId);

        if ($gift->to_user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Доступ запрещен'], 403);
        }

        if ($gift->status !== GiftUser::STATUS_PENDING) {
            return response()->json(['success' => false, 'message' => 'Подарок уже обработан'], 400);
        }

        $gift->status = GiftUser::STATUS_ACCEPTED;
        $gift->save();

        return response()->json([
            'success' => true,
            'message' => 'Подарок получен! Спасибо! 🎁'
        ]);
    }

// Отказаться от подарка (игнорировать)
    public function ignoreGift($giftId)
    {
        $gift = GiftUser::findOrFail($giftId);

        if ($gift->to_user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Доступ запрещен'], 403);
        }

        if ($gift->status !== GiftUser::STATUS_PENDING) {
            return response()->json(['success' => false, 'message' => 'Подарок уже обработан'], 400);
        }

        $gift->status = GiftUser::STATUS_IGNORED;
        $gift->save();

        return response()->json([
            'success' => true,
            'message' => 'Вы отказались от подарка'
        ]);
    }

// Страница мои подарки
    public function getMyGifts()
    {
        $receivedGifts = GiftUser::where('to_user_id', auth()->id())
            ->with(['gift', 'fromUser', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();

        $sentGifts = GiftUser::where('from_user_id', auth()->id())
            ->with(['gift', 'toUser', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cabinet.gifts.index', compact('receivedGifts', 'sentGifts'));
    }

// Получить количество неподтвержденных подарков
    public function getUnviewedGiftsCount()
    {
        return response()->json([
            'count' => GiftUser::where('to_user_id', auth()->id())
                ->where('status', 'pending')
                ->count()
        ]);
    }


}

<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventParticipantController extends Controller
{
    // Зарегистрироваться на событие
    public function register(Event $event)
    {
        // Проверяем, не зарегистрирован ли уже
        if ($event->isUserRegistered(Auth::id())) {
            return back()->with('error', 'Вы уже зарегистрированы на это событие');
        }

        // Проверяем, не заполнено ли максимальное количество мест
        if ($event->max_participants && $event->registered_count >= $event->max_participants) {
            return back()->with('error', 'Все места уже заняты');
        }

        // Регистрируем пользователя
        $event->users()->attach(Auth::id(), [
            'status' => 'registered',
            'registered_at' => now(),
            'ticket_number' => $this->generateTicketNumber($event->id, Auth::id()),
        ]);

        return back()->with('success', 'Вы успешно зарегистрировались на событие!');
    }

    // AJAX регистрация на событие
    public function ajaxRegister(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $event = Event::findOrFail($request->event_id);

        // Проверяем, не зарегистрирован ли уже
        if ($event->isUserRegistered(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже зарегистрированы на это событие',
                'status' => 'already_registered'
            ], 400);
        }

        // Проверяем, не заполнено ли максимальное количество мест
        if ($event->max_participants && $event->registered_count >= $event->max_participants) {
            return response()->json([
                'success' => false,
                'message' => 'Все места уже заняты',
                'status' => 'full'
            ], 400);
        }

        // Проверяем, не прошло ли событие
        if ($event->type === 'past') {
            return response()->json([
                'success' => false,
                'message' => 'Событие уже прошло',
                'status' => 'past'
            ], 400);
        }

        // Регистрируем пользователя
        $event->users()->attach(Auth::id(), [
            'status' => 'registered',
            'registered_at' => now(),
            'ticket_number' => $this->generateTicketNumber($event->id, Auth::id()),
        ]);

        // Получаем обновленную статистику
        $stats = [
            'registered_count' => $event->registered_count,
            'places_left' => $event->max_participants ? $event->max_participants - $event->registered_count : null
        ];

        return response()->json([
            'success' => true,
            'message' => 'Вы успешно зарегистрировались!',
            'status' => 'registered',
            'stats' => $stats
        ]);
    }

    // Подтвердить участие
    public function confirm(Event $event)
    {
        $record = $event->users()->where('user_id', Auth::id())->first();

        if (!$record) {
            return back()->with('error', 'Вы не зарегистрированы на это событие');
        }

        if ($record->pivot->status !== 'registered') {
            return back()->with('error', 'Невозможно подтвердить участие');
        }

        $event->users()->updateExistingPivot(Auth::id(), [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Участие подтверждено! Ждем вас.');
    }

    // Отметить участие (для администратора/организатора)
    public function markAttended(Event $event, $userId)
    {
        // Проверка прав (только организатор или админ)
        if (!Auth::user()->is_admin ?? false) {
            abort(403);
        }

        $record = $event->users()->where('user_id', $userId)->first();

        if (!$record) {
            return back()->with('error', 'Пользователь не зарегистрирован');
        }

        $event->users()->updateExistingPivot($userId, [
            'status' => 'attended',
            'attended_at' => now(),
        ]);

        // Обновляем счетчик участников
        $event->updateCurrentParticipants();

        return back()->with('success', 'Участие отмечено!');
    }

    // Отменить запись
    public function cancel(Event $event)
    {
        $record = $event->users()->where('user_id', Auth::id())->first();

        if (!$record) {
            return back()->with('error', 'Вы не зарегистрированы на это событие');
        }

        if (in_array($record->pivot->status, ['attended', 'cancelled'])) {
            return back()->with('error', 'Невозможно отменить запись');
        }

        $event->users()->updateExistingPivot(Auth::id(), [
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Запись на событие отменена');
    }

    // Генерация номера билета
    private function generateTicketNumber($eventId, $userId)
    {
        return 'TICKET-' . $eventId . '-' . $userId . '-' . time();
    }

    // Мои события (где зарегистрирован)
    public function myEvents()
    {
        $user = Auth::user();

        $registeredEvents = $user->registeredEvents()
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->get();

        $attendedEvents = $user->attendedEvents()
            ->orderBy('event_date', 'desc')
            ->take(10)
            ->get();

        $upcomingEvents = Event::upcoming()
            ->whereDoesntHave('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->take(6)
            ->get();

        return view('events.my-events', compact('registeredEvents', 'attendedEvents', 'upcomingEvents'));
    }
}

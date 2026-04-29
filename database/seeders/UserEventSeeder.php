<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserEventSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Создание 100 пользователей...');

        // 1. Создаем 100 пользователей
        $users = User::factory(100)->create();

        $this->command->info('Создаем профили для пользователей...');

        // 2. Создаем профили для каждого пользователя
        foreach ($users as $user) {
            Profile::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('Получаем будущие и прошедшие события...');

        // 3. Получаем события
        $upcomingEvents = Event::where('type', 'upcoming')->get();
        $pastEvents = Event::where('type', 'past')->get();

        $this->command->info('Будущих событий: ' . $upcomingEvents->count());
        $this->command->info('Прошедших событий: ' . $pastEvents->count());

        $this->command->info('Регистрируем пользователей на события...');

        // 4. Регистрируем пользователей на события
        $statuses = [
            'registered' => 40,  // 40% просто зарегистрированы
            'confirmed' => 25,   // 25% подтвердили
            'attended' => 25,    // 25% участвовали
            'cancelled' => 10,   // 10% отменили
        ];

        // Регистрация на будущие события
        foreach ($upcomingEvents as $event) {
            // Количество регистраций (от 10 до 80% от макс. участников или до 50)
            $maxRegistrations = $event->max_participants
                ? min($event->max_participants, 50)
                : fake()->numberBetween(10, 40);

            // Берем случайных пользователей
            $randomUsers = $users->random(min($maxRegistrations, $users->count()));

            foreach ($randomUsers as $user) {
                // Выбираем статус по весам
                $status = $this->getRandomStatus($statuses);

                $pivotData = [
                    'status' => $status,
                    'registered_at' => fake()->dateTimeBetween('-30 days', 'now'),
                    'ticket_number' => null,
                    'notes' => null,
                ];

                // Добавляем дополнительные поля в зависимости от статуса
                if ($status === 'confirmed') {
                    $pivotData['confirmed_at'] = fake()->dateTimeBetween('-20 days', 'now');
                }

                if ($status === 'attended') {
                    $pivotData['confirmed_at'] = fake()->dateTimeBetween('-20 days', '-5 days');
                    $pivotData['attended_at'] = fake()->dateTimeBetween('-5 days', $event->event_date);
                }

                if ($status === 'cancelled') {
                    $pivotData['registered_at'] = fake()->dateTimeBetween('-30 days', '-10 days');
                }

                $event->users()->attach($user->id, $pivotData);
            }

            $this->command->info("Событие '{$event->title}': зарегистрировано {$randomUsers->count()} пользователей");
        }

        // Регистрация на прошедшие события (в основном attended и cancelled)
        $pastStatuses = [
            'attended' => 60,   // 60% участвовали
            'cancelled' => 20,   // 20% отменили
            'registered' => 20,  // 20% зарегистрировались но не пришли
        ];

        foreach ($pastEvents as $event) {
            // Количество участников (меньше чем на будущие)
            $maxRegistrations = fake()->numberBetween(15, 45);
            $randomUsers = $users->random(min($maxRegistrations, $users->count()));

            foreach ($randomUsers as $user) {
                $status = $this->getRandomStatus($pastStatuses);

                $pivotData = [
                    'status' => $status,
                    'registered_at' => fake()->dateTimeBetween($event->event_date->copy()->subMonths(2), $event->event_date),
                    'ticket_number' => null,
                ];

                if (in_array($status, ['confirmed', 'attended'])) {
                    $pivotData['confirmed_at'] = fake()->dateTimeBetween(
                        $event->event_date->copy()->subMonth(),
                        $event->event_date->copy()->subDays(3)
                    );
                }

                if ($status === 'attended') {
                    $pivotData['attended_at'] = fake()->dateTimeBetween(
                        $event->event_date->copy()->subHours(2),
                        $event->event_date->copy()->addHours(3)
                    );
                }

                $event->users()->attach($user->id, $pivotData);
            }

            $this->command->info("Прошедшее событие '{$event->title}': {$randomUsers->count()} участников");
        }

        $this->command->info('Обновляем счетчики участников в событиях...');

        // 5. Обновляем current_participants для событий (только attended)
        foreach (Event::all() as $event) {
            $attendedCount = $event->users()->wherePivot('status', 'attended')->count();
            $event->current_participants = $attendedCount;
            $event->saveQuietly();
        }

        $this->command->info('Готово!');
        $this->showStatistics($users, $upcomingEvents, $pastEvents);
    }

    private function getRandomStatus($statuses)
    {
        $rand = fake()->numberBetween(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'registered';
    }

    private function showStatistics($users, $upcomingEvents, $pastEvents)
    {
        $this->command->info("\n========== СТАТИСТИКА ==========");
        $this->command->info("Всего пользователей: {$users->count()}");

        // Статистика по регистрациям на будущие события
        $totalUpcomingRegs = 0;
        $upcomingAttended = 0;

        foreach ($upcomingEvents as $event) {
            $regs = $event->users()->count();
            $att = $event->users()->wherePivot('status', 'attended')->count();
            $totalUpcomingRegs += $regs;
            $upcomingAttended += $att;
        }

        $this->command->info("\n--- Будущие события ---");
        $this->command->info("Всего регистраций: {$totalUpcomingRegs}");
        $this->command->info("Подтвержденных участников: {$upcomingAttended}");

        // Статистика по прошедшим событиям
        $totalPastRegs = 0;
        $pastAttended = 0;

        foreach ($pastEvents as $event) {
            $regs = $event->users()->count();
            $att = $event->users()->wherePivot('status', 'attended')->count();
            $totalPastRegs += $regs;
            $pastAttended += $att;
        }

        $this->command->info("\n--- Прошедшие события ---");
        $this->command->info("Всего регистраций: {$totalPastRegs}");
        $this->command->info("Участвовало: {$pastAttended}");

        // Распределение по статусам
        $statusStats = [
            'registered' => 0,
            'confirmed' => 0,
            'attended' => 0,
            'cancelled' => 0,
        ];

        foreach (Event::all() as $event) {
            foreach ($statusStats as $status => $count) {
                $statusStats[$status] += $event->users()->wherePivot('status', $status)->count();
            }
        }

        $this->command->info("\n--- Общее распределение по статусам ---");
        foreach ($statusStats as $status => $count) {
            $this->command->info("{$status}: {$count}");
        }

        $this->command->info("==================================\n");
    }
}

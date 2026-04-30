<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Gift;
use App\Models\User;
use App\Models\Wink;
use App\Models\GiftUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SpecialUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=========================================');
        $this->command->info('Создание специальных пользователей...');
        $this->command->info('=========================================');

        // 1. Создаем администратора
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Администратор',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info("✅ Администратор создан: admin@gmail.com / 11111111");

        // 2. Создаем тестового пользователя
        $testUser = User::updateOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name' => 'Тестовый Пользователь',
                'email' => 'test@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info("✅ Тестовый пользователь создан: test@gmail.com / 11111111");

        // 3. Получаем всех остальных пользователей (кто будет дарить подарки и подмигивать)
        $otherUsers = User::whereNotIn('id', [$admin->id, $testUser->id])->get();
        $this->command->info("👥 Найдено других пользователей: " . $otherUsers->count());

        // 4. Получаем все события
        $allEvents = Event::all();
        $upcomingEvents = Event::where('type', 'upcoming')->get();
        $pastEvents = Event::where('type', 'past')->get();

        $this->command->info("📅 Всего событий: " . $allEvents->count());
        $this->command->info("   - Будущих: " . $upcomingEvents->count());
        $this->command->info("   - Прошедших: " . $pastEvents->count());

        // ========== 5. РЕГИСТРАЦИЯ НА СОБЫТИЯ ==========
        $this->command->info("\n📝 Регистрация пользователей на события...");

        foreach ($allEvents as $event) {
            // Администратор регистрируется на все события
            if (!$event->isUserRegistered($admin->id)) {
                $event->users()->attach($admin->id, [
                    'status' => $event->type === 'past' ? 'attended' : 'registered',
                    'registered_at' => now(),
                    'confirmed_at' => $event->type === 'past' ? $event->event_date : null,
                    'attended_at' => $event->type === 'past' ? $event->event_date : null,
                ]);
            }

            // Тестовый пользователь регистрируется на все события
            if (!$event->isUserRegistered($testUser->id)) {
                $event->users()->attach($testUser->id, [
                    'status' => $event->type === 'past' ? 'attended' : 'registered',
                    'registered_at' => now(),
                    'confirmed_at' => $event->type === 'past' ? $event->event_date : null,
                    'attended_at' => $event->type === 'past' ? $event->event_date : null,
                ]);
            }
        }
        $this->command->info("✅ Админ и Тестовый пользователь зарегистрированы на все " . $allEvents->count() . " событий");

        // ========== 6. ПОДАРКИ ДЛЯ АДМИНА ==========
        $this->command->info("\n🎁 Создание подарков для Администратора...");

        $gifts = Gift::all();
        $giftIds = $gifts->pluck('id')->toArray();

        $adminGiftCount = 0;
        foreach ($pastEvents as $event) {
            // Количество подарков для админа на каждом событии (от 3 до 8)
            $giftCount = min(rand(3, 8), $otherUsers->count(), count($giftIds));
            $randomGiftIds = (array)array_rand(array_flip($giftIds), $giftCount);
            $randomUsers = $otherUsers->random(min($giftCount, $otherUsers->count()));

            $giftIndex = 0;
            foreach ($randomUsers as $giver) {
                if ($giftIndex >= count($randomGiftIds)) break;

                GiftUser::updateOrCreate(
                    [
                        'gift_id' => $randomGiftIds[$giftIndex],
                        'from_user_id' => $giver->id,
                        'to_user_id' => $admin->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'message' => 'Отличное мероприятие! Спасибо за организацию! 🎉'
                    ]
                );
                $adminGiftCount++;
                $giftIndex++;
            }
        }
        $this->command->info("✅ Администратор получил {$adminGiftCount} подарков");

        // ========== 7. ПОДАРКИ ДЛЯ ТЕСТОВОГО ПОЛЬЗОВАТЕЛЯ ==========
        $this->command->info("\n🎁 Создание подарков для Тестового пользователя...");

        $testGiftCount = 0;
        foreach ($pastEvents as $event) {
            $giftCount = min(rand(2, 6), $otherUsers->count(), count($giftIds));
            $randomGiftIds = (array)array_rand(array_flip($giftIds), $giftCount);
            $randomUsers = $otherUsers->random(min($giftCount, $otherUsers->count()));

            $giftIndex = 0;
            foreach ($randomUsers as $giver) {
                if ($giftIndex >= count($randomGiftIds)) break;

                GiftUser::updateOrCreate(
                    [
                        'gift_id' => $randomGiftIds[$giftIndex],
                        'from_user_id' => $giver->id,
                        'to_user_id' => $testUser->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'message' => 'Приятно было познакомиться! Вот тебе подарочек! 🎁'
                    ]
                );
                $testGiftCount++;
                $giftIndex++;
            }
        }
        $this->command->info("✅ Тестовый пользователь получил {$testGiftCount} подарков");

        // ========== 8. ПОДМИГИВАНИЯ (ЛАЙКИ) ДЛЯ АДМИНА ==========
        $this->command->info("\n😉 Создание подмигиваний для Администратора...");

        $adminWinkCount = 0;
        $adminAcceptedCount = 0;

        foreach ($allEvents as $event) {
            // Случайные пользователи подмигивают админу
            $winkCount = min(rand(3, 12), $otherUsers->count());
            $randomUsers = $otherUsers->random($winkCount);

            foreach ($randomUsers as $giver) {
                $existingWink = Wink::where('from_user_id', $giver->id)
                    ->where('to_user_id', $admin->id)
                    ->where('event_id', $event->id)
                    ->first();

                if (!$existingWink) {
                    $status = rand(1, 100) <= 70 ? 'accepted' : 'pending'; // 70% подтверждено

                    Wink::create([
                        'from_user_id' => $giver->id,
                        'to_user_id' => $admin->id,
                        'event_id' => $event->id,
                        'is_viewed' => true,
                        'status' => $status,
                    ]);
                    $adminWinkCount++;
                    if ($status === 'accepted') $adminAcceptedCount++;
                }
            }
        }
        $this->command->info("✅ Администратор получил {$adminWinkCount} подмигиваний (из них подтверждено: {$adminAcceptedCount})");

        // ========== 9. ПОДМИГИВАНИЯ (ЛАЙКИ) ДЛЯ ТЕСТОВОГО ПОЛЬЗОВАТЕЛЯ ==========
        $this->command->info("\n😉 Создание подмигиваний для Тестового пользователя...");

        $testWinkCount = 0;
        $testAcceptedCount = 0;

        foreach ($allEvents as $event) {
            $winkCount = min(rand(2, 10), $otherUsers->count());
            $randomUsers = $otherUsers->random($winkCount);

            foreach ($randomUsers as $giver) {
                $existingWink = Wink::where('from_user_id', $giver->id)
                    ->where('to_user_id', $testUser->id)
                    ->where('event_id', $event->id)
                    ->first();

                if (!$existingWink) {
                    $status = rand(1, 100) <= 60 ? 'accepted' : 'pending'; // 60% подтверждено

                    Wink::create([
                        'from_user_id' => $giver->id,
                        'to_user_id' => $testUser->id,
                        'event_id' => $event->id,
                        'is_viewed' => true,
                        'status' => $status,
                    ]);
                    $testWinkCount++;
                    if ($status === 'accepted') $testAcceptedCount++;
                }
            }
        }
        $this->command->info("✅ Тестовый пользователь получил {$testWinkCount} подмигиваний (из них подтверждено: {$testAcceptedCount})");

        // ========== 10. ВЗАИМНЫЕ ПОДМИГИВАНИЯ ==========
        $this->command->info("\n💕 Создание взаимных подмигиваний...");

        $mutualCount = 0;
        foreach ($allEvents as $event) {
            // Админ подмигивает некоторым в ответ
            $adminMutualUsers = $otherUsers->random(min(rand(2, 5), $otherUsers->count()));
            foreach ($adminMutualUsers as $user) {
                $existingWink = Wink::where('from_user_id', $admin->id)
                    ->where('to_user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->first();

                if (!$existingWink) {
                    Wink::create([
                        'from_user_id' => $admin->id,
                        'to_user_id' => $user->id,
                        'event_id' => $event->id,
                        'is_viewed' => true,
                        'status' => 'accepted',
                    ]);

                    // Обновляем статус встречного подмигивания
                    Wink::where('from_user_id', $user->id)
                        ->where('to_user_id', $admin->id)
                        ->where('event_id', $event->id)
                        ->update(['status' => 'accepted']);

                    $mutualCount++;
                }
            }
        }
        $this->command->info("✅ Создано {$mutualCount} взаимных подмигиваний");

        // ========== 11. ОБНОВЛЕНИЕ СЧЕТЧИКОВ УЧАСТНИКОВ ==========
        $this->command->info("\n🔄 Обновление счетчиков участников событий...");

        foreach (Event::all() as $event) {
            $attendedCount = $event->users()->wherePivot('status', 'attended')->count();
            $event->current_participants = $attendedCount;
            $event->saveQuietly();
        }
        $this->command->info("✅ Счетчики обновлены");

        // ========== 12. ВЫВОД ИТОГОВОЙ СТАТИСТИКИ ==========
        $this->command->info("\n=========================================");
        $this->command->info("📊 ИТОГОВАЯ СТАТИСТИКА");
        $this->command->info("=========================================");
        $this->command->info("");
        $this->command->info("👤 АДМИНИСТРАТОР:");
        $this->command->info("   📧 Email: admin@gmail.com");
        $this->command->info("   🔑 Пароль: 11111111");
        $this->command->info("   🎁 Получено подарков: {$adminGiftCount}");
        $this->command->info("   😉 Получено подмигиваний: {$adminWinkCount}");
        $this->command->info("");
        $this->command->info("👤 ТЕСТОВЫЙ ПОЛЬЗОВАТЕЛЬ:");
        $this->command->info("   📧 Email: test@gmail.com");
        $this->command->info("   🔑 Пароль: 11111111");
        $this->command->info("   🎁 Получено подарков: {$testGiftCount}");
        $this->command->info("   😉 Получено подмигиваний: {$testWinkCount}");
        $this->command->info("");
        $this->command->info("💕 Взаимных подмигиваний: {$mutualCount}");
        $this->command->info("=========================================");
        $this->command->info("✅ Сидер успешно выполнен!");
    }
}

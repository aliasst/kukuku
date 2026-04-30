<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $this->call([
            EventSeeder::class,
        ]);

        // Затем создаем пользователей и регистрируем их на события
        $this->call(UserEventSeeder::class);

        $this->call(GiftSeeder::class);

        // Создаем специальных пользователей (админ, тестовый)
        $this->call(SpecialUsersSeeder::class);

    }
}

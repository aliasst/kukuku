<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        $genders = ['male', 'female', 'other'];

        return [
            'user_id' => User::factory(),
            'full_name' => fake('ru_RU')->name(),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'gender' => fake()->randomElement($genders),
            'phone' => fake()->phoneNumber(),
            'telegram' => '@' . fake()->userName(),
            'visit_purpose' => fake()->randomElement([
                'Хочу участвовать в мероприятиях',
                'Поиск новых знакомств',
                'Профессиональное развитие',
                'Обучение и курсы',
                'Развлечение и отдых',
                'Нет цели, просто интересно'
            ]),
            'avatar' => null,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}

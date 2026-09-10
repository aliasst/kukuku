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
            'full_name' => $this->faker->name(),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'gender' => $this->faker->randomElement($genders),
            'phone' => $this->faker->phoneNumber(),
            'telegram' => '@' . $this->faker->userName(),
            'visit_purpose' => $this->faker->randomElement([
                'Хочу участвовать в мероприятиях',
                'Поиск новых знакомств',
                'Профессиональное развитие',
                'Обучение и курсы',
                'Развлечение и отдых',
                'Нет цели, просто интересно'
            ]),
            'avatar' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}

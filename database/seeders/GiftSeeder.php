<?php

namespace Database\Seeders;

use App\Models\Gift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gifts = [
            ['name' => 'Конфета', 'icon' => 'img/gifts/candy.png', 'price' => 0, 'color' => '#ff6b6b', 'sort_order' => 1],
            ['name' => 'Букет цветов', 'icon' => 'img/gifts/bouquet.png', 'price' => 0, 'color' => '#4ecdc4', 'sort_order' => 2],
            ['name' => 'Шампанское', 'icon' => 'img/gifts/bottle.png', 'price' => 0, 'color' => '#45b7d1', 'sort_order' => 3],
            ['name' => 'Роза', 'icon' => 'img/gifts/rose.png', 'price' => 0, 'color' => '#96ceb4', 'sort_order' => 4],
            ['name' => 'Торт', 'icon' => 'img/gifts/cake.png', 'price' => 0, 'color' => '#fcca6f', 'sort_order' => 5],
            ['name' => 'Фужеры', 'icon' => 'img/gifts/glasses.png', 'price' => 0, 'color' => '#ff6b6b', 'sort_order' => 6],
            ['name' => 'Пончик', 'icon' => 'img/gifts/doughnut.png', 'price' => 0, 'color' => '#a06bff', 'sort_order' => 7],
            ['name' => 'Сюрприз', 'icon' => 'img/gifts/gift.png', 'price' => 0, 'color' => '#4ecdc4', 'sort_order' => 8],
            ['name' => 'Мороженое', 'icon' => 'img/gifts/ice-cream.png', 'price' => 0, 'color' => '#4ecdc4', 'sort_order' => 9],
            ['name' => 'Напиток', 'icon' => 'img/gifts/drink.png', 'price' => 0, 'color' => '#4ecdc4', 'sort_order' => 10],
        ];

        foreach ($gifts as $gift) {
            Gift::create($gift);
        }

        $this->command->info('Создано ' . count($gifts) . ' подарков');
    }
}

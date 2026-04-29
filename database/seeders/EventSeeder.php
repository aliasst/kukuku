<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Будущие события (10 штук)
        $upcomingEvents = [
            [
                'title' => 'Конференция',
                'description' => 'Крупнейшая конференция для веб-разработчиков в 2024 году',
                'content' => '<p>Приглашаем всех веб-разработчиков на ежегодную международную конференцию.</p>
                              <p>В программе:</p>
                              <ul>
                                <li>Современные фреймворки</li>
                                <li>AI в веб-разработке</li>
                                <li>Безопасность веб-приложений</li>
                                <li>Микрофронтенды</li>
                              </ul>
                              <p>Выступят ведущие эксперты из разных стран.</p>',
                'event_date' => now()->addDays(7),
                'event_time' => '10:00',
                'location' => 'Москва, Крокус Экспо, зал 3',
                'price' => 5000,
                'max_participants' => 200,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Мастер-класс по Laravel',
                'description' => 'Практический мастер-класс по новым возможностям Laravel',
                'content' => '<p>На мастер-классе разберем новые фичи Laravel 11.</p>
                              <p>Что будет:</p>
                              <ul>
                                <li>Новые возможности Eloquent</li>
                                <li>Улучшенная производительность</li>
                                <li>Native типизация</li>
                              </ul>',
                'event_date' => now()->addDays(3),
                'event_time' => '14:00',
                'location' => 'Санкт-Петербург, БЦ "Алые Паруса"',
                'price' => 1500,
                'max_participants' => 50,
                'is_online' => true,
                'online_link' => 'https://zoom.us/join/123456',
                'image' => null,
            ],
            [
                'title' => 'Цифровые инновации"',
                'description' => '48-часовой хакатон для разработчиков, дизайнеров и продуктов',
                'content' => '<p>Хакатон с призовым фондом 500 000 рублей.</p>
                              <p>Номинации:</p>
                              <ul>
                                <li>Лучшее мобильное приложение</li>
                                <li>AI проект</li>
                                <li>Социальный проект</li>
                              </ul>',
                'event_date' => now()->addDays(14),
                'event_time' => '09:00',
                'location' => 'Екатеринбург, Технопарк',
                'price' => 0,
                'max_participants' => 150,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Воркшоп по Vue.js 3',
                'description' => 'Интенсив по Vue.js 3 для начинающих и опытных разработчиков',
                'content' => '<p>За 1 день научимся создавать реактивные приложения на Vue 3.</p>
                              <p>Темы:</p>
                              <ul>
                                <li>Composition API</li>
                                <li>Pinia для управления состоянием</li>
                                <li>Роутинг и защита маршрутов</li>
                              </ul>',
                'event_date' => now()->addDays(10),
                'event_time' => '11:00',
                'location' => 'Новосибирск, Академгородок',
                'price' => 2000,
                'max_participants' => 40,
                'is_online' => true,
                'online_link' => 'https://teams.microsoft.com/join/789012',
                'image' => null,
            ],
            [
                'title' => 'Курс Python"',
                'description' => '5-дневный курс по анализу данных на Python',
                'content' => '<p>Научим работать с Pandas, NumPy, Matplotlib.</p>
                              <p>Итоговый проект: анализ реального датасета.</p>',
                'event_date' => now()->addDays(21),
                'event_time' => '10:00',
                'location' => 'Казань, IT-парк',
                'price' => 8000,
                'max_participants' => 30,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Вебинар DevOps',
                'description' => 'Бесплатный вебинар о профессии DevOps',
                'content' => '<p>Расскажем что нужно знать DevOps-инженеру.</p>
                              <p>План вебинара:</p>
                              <ul>
                                <li>Основы Linux</li>
                                <li>Docker контейнеризация</li>
                                <li>CI/CD пайплайны</li>
                              </ul>',
                'event_date' => now()->addDays(5),
                'event_time' => '19:00',
                'location' => 'Онлайн',
                'price' => 0,
                'max_participants' => 500,
                'is_online' => true,
                'online_link' => 'https://youtube.com/live/webinar',
                'image' => null,
            ],
            [
                'title' => 'Конференция 2',
                'description' => 'AI Conf 2024 - главное событие года про искусственный интеллект',
                'content' => '<p>Ведущие эксперты по машинному обучению и нейросетям.</p>
                              <p>Ключевые темы:</p>
                              <ul>
                                <li>LLM модели</li>
                                <li>Генеративный AI</li>
                                <li>Этика в AI</li>
                              </ul>',
                'event_date' => now()->addDays(30),
                'event_time' => '10:00',
                'location' => 'Сочи, Конгресс-центр',
                'price' => 12000,
                'max_participants' => 500,
                'is_online' => true,
                'online_link' => 'https://aiconf.ru/online',
                'image' => null,
            ],
            [
                'title' => 'Митап Frontend',
                'description' => 'Встреча фронтендеров с решением сложных задач',
                'content' => '<p>Решаем реальные фронтенд задачи в формате code battle.</p>
                              <p>Призы и мерч от спонсоров.</p>',
                'event_date' => now()->addDays(12),
                'event_time' => '18:30',
                'location' => 'Краснодар, Coworking Space',
                'price' => 0,
                'max_participants' => 50,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Интенсив по базам',
                'description' => 'Практикум по SQL, индексам и оптимизации запросов',
                'content' => '<p>Для бэкенд разработчиков и аналитиков.</p>
                              <p>Программа:</p>
                              <ul>
                                <li>Сложные выборки JOIN</li>
                                <li>Оконные функции</li>
                                <li>План выполнения запросов</li>
                              </ul>',
                'event_date' => now()->addDays(18),
                'event_time' => '11:00',
                'location' => 'Ростов-на-Дону, БЦ "Петровский"',
                'price' => 3000,
                'max_participants' => 35,
                'is_online' => true,
                'online_link' => 'https://meet.google.com/sql-2024',
                'image' => null,
            ],
            [
                'title' => 'Тестирование',
                'description' => 'Конференция для QA инженеров',
                'content' => '<p>Все о современном тестировании.</p>
                              <p>Темы:</p>
                              <ul>
                                <li>Автотесты на Python</li>
                                <li>Нагрузочное тестирование</li>
                                <li>Кибербезопасность в тестировании</li>
                              </ul>',
                'event_date' => now()->addDays(25),
                'event_time' => '10:00',
                'location' => 'Воронеж, Конференц-зал',
                'price' => 2500,
                'max_participants' => 60,
                'is_online' => false,
                'image' => null,
            ],
        ];

        // Прошедшие события (10 штук)
        $pastEvents = [
            [
                'title' => 'Laravel Meetup',
                'description' => 'Встреча Laravel разработчиков',
                'content' => '<p>Обсудили новые фичи Laravel 10 и обменялись опытом.</p>',
                'event_date' => now()->subDays(5),
                'event_time' => '19:00',
                'location' => 'Москва, Time Space',
                'price' => 0,
                'max_participants' => 40,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Веб-разработка',
                'description' => 'Обзор трендов в веб-разработке',
                'content' => '<p>Поговорили о Server Components, Astro, Tailwind.</p>',
                'event_date' => now()->subDays(12),
                'event_time' => '16:00',
                'location' => 'Санкт-Петербург, Точка Кипения',
                'price' => 500,
                'max_participants' => 80,
                'is_online' => true,
                'online_link' => 'https://youtube.com/watch/web2023',
                'image' => null,
            ],
            [
                'title' => 'Бесплатный курс',
                'description' => 'Вводный курс по системе контроля версий Git',
                'content' => '<p>Научились основам Git, работе с ветками и GitHub.</p>',
                'event_date' => now()->subDays(8),
                'event_time' => '15:00',
                'location' => 'Онлайн',
                'price' => 0,
                'max_participants' => 200,
                'is_online' => true,
                'online_link' => 'https://zoom.us/join/git2023',
                'image' => null,
            ],
            [
                'title' => 'PHP-дайджест',
                'description' => 'Обзор новостей мира PHP',
                'content' => '<p>Обсудили PHP 8.3, новинки фреймворков.</p>',
                'event_date' => now()->subDays(20),
                'event_time' => '18:00',
                'location' => 'Новосибирск',
                'price' => 300,
                'max_participants' => 30,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Docker',
                'description' => 'Практический воркшоп по Docker',
                'content' => '<p>Научились контейнеризировать приложения.</p>',
                'event_date' => now()->subDays(15),
                'event_time' => '10:00',
                'location' => 'Екатеринбург',
                'price' => 0,
                'max_participants' => 45,
                'is_online' => true,
                'online_link' => 'https://meet.google.com/docker',
                'image' => null,
            ],
            [
                'title' => 'Микрофронтенды',
                'description' => 'Конференция о микрофронтендах',
                'content' => '<p>Поделились кейсами использования Module Federation.</p>',
                'event_date' => now()->subDays(25),
                'event_time' => '11:00',
                'location' => 'Казань, IT Park',
                'price' => 1500,
                'max_participants' => 55,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Kubernetes',
                'description' => 'Введение в оркестрацию контейнеров',
                'content' => '<p>Разобрали основные концепции Kubernetes.</p>',
                'event_date' => now()->subDays(18),
                'event_time' => '13:00',
                'location' => 'Ростов-на-Дону',
                'price' => 2000,
                'max_participants' => 25,
                'is_online' => true,
                'online_link' => 'https://teams.com/k8s',
                'image' => null,
            ],
            [
                'title' => 'REST API',
                'description' => 'Мастер-класс по проектированию API',
                'content' => '<p>Научились проектировать качественные REST API.</p>',
                'event_date' => now()->subDays(10),
                'event_time' => '14:00',
                'location' => 'Краснодар',
                'price' => 800,
                'max_participants' => 40,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Технологии 2023',
                'description' => 'Конференция о главных технологических итогах',
                'content' => '<p>Подвели итоги года в IT индустрии.</p>',
                'event_date' => now()->subDays(30),
                'event_time' => '10:00',
                'location' => 'Москва, Дизайн-завод',
                'price' => 3000,
                'max_participants' => 150,
                'is_online' => false,
                'image' => null,
            ],
            [
                'title' => 'Безопасность',
                'description' => 'Семинар по кибербезопасности',
                'content' => '<p>Разобрали основные уязвимости и способы защиты.</p>',
                'event_date' => now()->subDays(22),
                'event_time' => '15:00',
                'location' => 'Онлайн',
                'price' => 0,
                'max_participants' => 100,
                'is_online' => true,
                'online_link' => 'https://security-webinar.com',
                'image' => null,
            ],
        ];

        // Создаем будущие события
        foreach ($upcomingEvents as $eventData) {
            $eventData['slug'] = Str::slug($eventData['title']);
            $eventData['is_free'] = $eventData['price'] == 0;
            $eventData['type'] = 'upcoming';
            $eventData['status'] = 'active';
            Event::create($eventData);
        }

        // Создаем прошедшие события
        foreach ($pastEvents as $eventData) {
            $eventData['slug'] = Str::slug($eventData['title']);
            $eventData['is_free'] = $eventData['price'] == 0;
            $eventData['type'] = 'past';
            $eventData['status'] = 'completed';
            Event::create($eventData);
        }

        $this->command->info('Создано 20 событий: 10 будущих и 10 прошедших!');
    }
}

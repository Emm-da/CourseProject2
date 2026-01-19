<?php
// recommendations.php - Умные рекомендации и советы
require_once 'header.php'; 

// Получаем данные для рекомендаций
function getApiData($endpoint) {
    $url = "http://localhost:8080/api" . $endpoint;
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    return $response ? json_decode($response, true) : null;
}

$stats = getApiData('/venues/stats');
$allVenues = getApiData('/venues/all');
$districts = getApiData('/venues/districts');

// Массив месяцев с рекомендациями
$monthRecommendations = [
    'Январь' => [
        'top_venues' => [
            [
                'name' => 'Зимняя эстрада "Снежная"',
                'district' => 'район Сокольники',
                'reason' => 'Крытая площадка с отоплением',
                'badge' => 'badge-popular',
                'badgeText' => 'Популярно зимой'
            ],
            [
                'name' => 'Концертный зал "Морозко"',
                'district' => 'Таганский район',
                'reason' => 'Лучшая акустика для зимних концертов',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Каминный зал "Уют"',
                'district' => 'район Якиманка',
                'reason' => 'Идеально для камерных мероприятий',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '❄️ <strong>Зимние советы:</strong> Выбирайте крытые площадки с отоплением. Январь - лучшее время для камерных концертов в уютных залах.',
        'urgency' => 'Низкий сезон - много свободных мест, можно торговаться'
    ],
    'Февраль' => [
        'top_venues' => [
            [
                'name' => 'Романтическая эстрада',
                'district' => 'Пресненский район',
                'reason' => 'Специальные программы для Дня влюбленных',
                'badge' => 'badge-popular',
                'badgeText' => 'Спрос высокий'
            ],
            [
                'name' => 'Концертный зал "Февраль"',
                'district' => 'район Хамовники',
                'reason' => 'Отличное отопление и вентиляция',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Малый зал "Искра"',
                'district' => 'район Арбат',
                'reason' => 'Уютная атмосфера для зимних вечеров',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '❤️ <strong>Февральские мероприятия:</strong> Идеальное время для романтических концертов. Бронируйте заранее на 14 февраля.',
        'urgency' => 'Средняя загруженность - бронировать за 2-3 недели'
    ],
    'Март' => [
        'top_venues' => [
            [
                'name' => 'Весенняя сцена "Проталинка"',
                'district' => 'район Измайлово',
                'reason' => 'Первая открытая площадка сезона',
                'badge' => 'badge-popular',
                'badgeText' => 'Новинка сезона'
            ],
            [
                'name' => 'Концертный зал "Весна"',
                'district' => 'район Сокольники',
                'reason' => 'Сезон открыт, цены доступны',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Терраса "Мартовские коты"',
                'district' => 'Таганский район',
                'reason' => 'Теплые дни, красивые виды',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌷 <strong>Весеннее пробуждение:</strong> В марте открываются первые уличные площадки. Идеально для джазовых и фолк-концертов.',
        'urgency' => 'Начинается сезон - бронировать за 3-4 недели'
    ],
    'Апрель' => [
        'top_venues' => [
            [
                'name' => 'Цветущая эстрада',
                'district' => 'район Якиманка',
                'reason' => 'В окружении цветущих садов',
                'badge' => 'badge-popular',
                'badgeText' => 'Популярно весной'
            ],
            [
                'name' => 'Садовая сцена "Апрель"',
                'district' => 'район Хамовники',
                'reason' => 'Идеально для open-air мероприятий',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Парковая площадка "Первоцвет"',
                'district' => 'район Сокольники',
                'reason' => 'Отличные виды, свежий воздух',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌸 <strong>Апрельские концерты:</strong> Идеальное время для открытых мероприятий. Погода переменчива - выбирайте площадки с навесом.',
        'urgency' => 'Высокий спрос - бронировать за 4-5 недель'
    ],
    'Май' => [
        'top_venues' => [
            [
                'name' => 'Майская эстрада "Победа"',
                'district' => 'Пресненский район',
                'reason' => 'Центральная площадка для праздников',
                'badge' => 'badge-popular',
                'badgeText' => 'Очень популярно'
            ],
            [
                'name' => 'Парковая сцена "Цветение"',
                'district' => 'район Сокольники',
                'reason' => 'В окружении цветущих яблонь и сирени',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Летняя терраса "Май"',
                'district' => 'район Якиманка',
                'reason' => 'Раннее открытие летнего сезона',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌼 <strong>Майские праздники:</strong> Пик сезона открытых площадок. Идеально для фестивалей и массовых мероприятий.',
        'urgency' => 'Очень высокая загруженность - бронировать за 5-6 недель'
    ],
    'Июнь' => [
        'top_venues' => [
            [
                'name' => 'Летняя эстрада "Солнце"',
                'district' => 'район Хамовники',
                'reason' => 'Популярна для выпускных вечеров',
                'badge' => 'badge-popular',
                'badgeText' => 'Высокий спрос'
            ],
            [
                'name' => 'Ночная сцена "Белые ночи"',
                'district' => 'Таганский район',
                'reason' => 'Специальные ночные программы',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Речная площадка "Июнь"',
                'district' => 'район Якиманка',
                'reason' => 'Вид на Москву-реку, прохлада',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '☀️ <strong>Летний сезон:</strong> Идеальное время для фестивалей и ночных концертов. Выбирайте площадки с навесом от солнца.',
        'urgency' => 'Высокий сезон - бронировать за 4-6 недель'
    ],
    'Июль' => [
        'top_venues' => [
            [
                'name' => 'Пляжная эстрада "Лето"',
                'district' => 'район Сокольники',
                'reason' => 'Рядом с пляжной зоной',
                'badge' => 'badge-popular',
                'badgeText' => 'Летний хит'
            ],
            [
                'name' => 'Вечерняя сцена "Жара"',
                'district' => 'Пресненский район',
                'reason' => 'Работает до поздней ночи',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Тенистая терраса "Прохлада"',
                'district' => 'район Хамовники',
                'reason' => 'В тени вековых деревьев',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌞 <strong>Июльская жара:</strong> Выбирайте площадки в тени или рядом с водой. Вечерние концерты пользуются наибольшим спросом.',
        'urgency' => 'Средняя загруженность - бронировать за 3-4 недели'
    ],
    'Август' => [
        'top_venues' => [
            [
                'name' => 'Фестивальная сцена "Август"',
                'district' => 'район Якиманка',
                'reason' => 'Специально для летних фестивалей',
                'badge' => 'badge-popular',
                'badgeText' => 'Фестивальный сезон'
            ],
            [
                'name' => 'Звездная эстрада "Метеор"',
                'district' => 'Таганский район',
                'reason' => 'Идеально для наблюдения за метеорами',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Вечерняя терраса "Закат"',
                'district' => 'район Сокольники',
                'reason' => 'Лучшие виды на закаты августа',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌠 <strong>Августовские звезды:</strong> Идеальное время для ночных концертов и наблюдения за звездопадом. Температура комфортная.',
        'urgency' => 'Высокий спрос - бронировать за 4-5 недель'
    ],
    'Сентябрь' => [
        'top_venues' => [
            [
                'name' => 'Осенняя эстрада "Золото"',
                'district' => 'район Хамовники',
                'reason' => 'В окружении золотых листьев',
                'badge' => 'badge-popular',
                'badgeText' => 'Осенний фаворит'
            ],
            [
                'name' => 'Учебная сцена "Сентябрь"',
                'district' => 'Пресненский район',
                'reason' => 'Популярна для студенческих мероприятий',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Парковая площадка "Листопад"',
                'district' => 'район Сокольники',
                'reason' => 'Романтическая атмосфера осени',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🍂 <strong>Бархатный сезон:</strong> Идеальная погода для мероприятий на открытом воздухе. Цены становятся доступнее после летнего пика.',
        'urgency' => 'Средняя загруженность - бронировать за 3-4 недели'
    ],
    'Октябрь' => [
        'top_venues' => [
            [
                'name' => 'Золотая сцена "Октябрь"',
                'district' => 'Таганский район',
                'reason' => 'Последние теплые дни сезона',
                'badge' => 'badge-popular',
                'badgeText' => 'Популярно осенью'
            ],
            [
                'name' => 'Камерный зал "Уют"',
                'district' => 'район Якиманка',
                'reason' => 'Идеален для дождливых дней',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Осенняя терраса "Тепло"',
                'district' => 'район Хамовники',
                'reason' => 'С подогревом и навесом',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🍁 <strong>Осенние концерты:</strong> Переходный месяц - есть и открытые, и закрытые площадки. Рекомендуем площадки с навесом.',
        'urgency' => 'Низкая загруженность - можно бронировать за 2-3 недели'
    ],
    'Ноябрь' => [
        'top_venues' => [
            [
                'name' => 'Дождливая эстрада "Ноябрь"',
                'district' => 'район Сокольники',
                'reason' => 'С крышей и обогревом',
                'badge' => 'badge-popular',
                'badgeText' => 'Защита от непогоды'
            ],
            [
                'name' => 'Теплый зал "У камина"',
                'district' => 'Пресненский район',
                'reason' => 'Уютная атмосфера для холодных дней',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Концертная гостиная "Осень"',
                'district' => 'Таганский район',
                'reason' => 'Идеально для камерных концертов',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🌧️ <strong>Ноябрьская непогода:</strong> Переходим на закрытые площадки. Идеальное время для джазовых и классических концертов.',
        'urgency' => 'Низкий сезон - много свободных мест, доступные цены'
    ],
    'Декабрь' => [
        'top_venues' => [
            [
                'name' => 'Новогодняя эстрада',
                'district' => 'район Якиманка',
                'reason' => 'Специальные новогодние программы',
                'badge' => 'badge-popular',
                'badgeText' => 'Очень популярно'
            ],
            [
                'name' => 'Праздничный зал "Снежинка"',
                'district' => 'район Хамовники',
                'reason' => 'Новогоднее оформление включено',
                'badge' => 'badge-available',
                'badgeText' => 'Есть места'
            ],
            [
                'name' => 'Зимняя терраса "Мороз"',
                'district' => 'Таганский район',
                'reason' => 'С подогревом и горячими напитками',
                'badge' => 'badge-affordable',
                'badgeText' => 'Доступно'
            ]
        ],
        'advice' => '🎄 <strong>Новогодний сезон:</strong> Пик спроса на праздничные мероприятия. Бронируйте заранее, особенно на 31 декабря.',
        'urgency' => 'Очень высокая загруженность - бронировать за 6-8 недель'
    ]
];
?>

<!-- Стили только для контента страницы рекомендаций -->
<style>
    .recommendations-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
        border-radius: 0 0 30px 30px;
        margin-bottom: 40px;
    }
    
    .hero-title {
        font-size: 2.8em;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .hero-subtitle {
        font-size: 1.2em;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto 30px;
    }
    
    .recommendation-filters {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    
    .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .filters-header h2 {
        color: #2c3e50;
        margin: 0;
        font-size: 1.8em;
    }
    
    .filter-buttons {
        display: flex;
        gap: 15px;
    }
    
    .apply-btn {
        padding: 12px 30px;
        background: #4a6fa5;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }
    
    .apply-btn:hover {
        background: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .apply-btn:active {
        transform: translateY(0);
    }
    
    .reset-btn {
        padding: 12px 30px;
        background: #7f8c8d;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }
    
    .reset-btn:hover {
        background: #6c7b7d;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .reset-btn:active {
        transform: translateY(0);
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }
    
    .filter-group {
        margin-bottom: 10px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 12px;
        font-weight: bold;
        color: #2c3e50;
        font-size: 1.1em;
    }
    
    .filter-select {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
        background: white;
        cursor: pointer;
    }
    
    .filter-select:focus {
        border-color: #4a6fa5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
    }
    
    .recommendation-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border-top: 8px solid;
        transition: transform 0.3s;
        animation: fadeInUp 0.6s ease-out;
    }
    
    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    
    .card-top-choice {
        border-color: #e74c3c;
    }
    
    .card-format-advice {
        border-color: #2ecc71;
    }
    
    .card-urgency {
        border-color: #f39c12;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .card-icon {
        font-size: 3em;
        flex-shrink: 0;
    }
    
    .card-title {
        color: #2c3e50;
        margin: 0;
        font-size: 1.8em;
    }
    
    .card-subtitle {
        color: #7f8c8d;
        margin-top: 5px;
        font-size: 1.1em;
    }
    
    .venue-recommendations {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin: 30px 0;
    }
    
    .venue-recommendation {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 5px solid;
        transition: all 0.3s;
        border-color: #4a6fa5;
    }
    
    .venue-recommendation:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }
    
    .recommendation-badge {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-popular {
        background: #e74c3c;
        color: white;
    }
    
    .badge-available {
        background: #2ecc71;
        color: white;
    }
    
    .badge-affordable {
        background: #f39c12;
        color: white;
    }
    
    .ai-suggestion {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin: 30px 0;
        animation: fadeIn 0.8s ease-out;
    }
    
    .ai-suggestion h3 {
        margin-bottom: 15px;
        font-size: 1.5em;
    }
    
    .booking-timeline {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 15px;
        margin: 30px 0;
    }
    
    .timeline-header {
        margin-bottom: 25px;
    }
    
    .timeline-header h3 {
        color: #2c3e50;
        font-size: 1.4em;
        margin-bottom: 10px;
    }
    
    .timeline-month {
        color: #7f8c8d;
        font-size: 1.1em;
    }
    
    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .timeline-date {
        flex-shrink: 0;
        width: 100px;
        font-weight: bold;
        color: #4a6fa5;
        font-size: 1.1em;
    }
    
    .timeline-content {
        flex: 1;
    }
    
    .timeline-status {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .status-busy {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-moderate {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-available {
        background: #d4edda;
        color: #155724;
    }
    
    .current-selection {
        background: #e8f4fc;
        padding: 15px 20px;
        border-radius: 10px;
        margin-top: 25px;
        border-left: 4px solid #4a6fa5;
    }
    
    .selection-title {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .selection-text {
        color: #555;
        font-size: 0.95em;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: #2ecc71;
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification.hidden {
        animation: slideOutRight 0.3s ease-out forwards;
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .no-results {
        text-align: center;
        padding: 50px;
        color: #7f8c8d;
        font-size: 1.2em;
    }
    
    /* Стили для кнопок навигации */
    .nav-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 40px 0;
        flex-wrap: wrap;
    }
    
    .nav-btn {
        padding: 12px 25px;
        background: #4a6fa5;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .nav-btn:hover {
        background: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .nav-btn.secondary {
        background: #7f8c8d;
    }
    
    .nav-btn.secondary:hover {
        background: #6c7b7d;
    }
</style>

</head>
<body>
    <!-- Шапка уже подключена через header.php -->
    
    <main>
        <!-- Герой-секция -->
        <section class="recommendations-hero">
            <div class="container">
                <h1 class="hero-title">💡 Умные рекомендации</h1>
                <p class="hero-subtitle">Персональные советы для выбора идеальной музыкальной площадки в Москве</p>
            </div>
        </section>
        
        <div class="container">
            <!-- Фильтры для рекомендаций -->
            <div class="recommendation-filters">
                <div class="filters-header">
                    <h2>Настройте рекомендации</h2>
                    <div class="filter-buttons">
                        <button id="applyFilters" class="apply-btn">
                            <span>Применить фильтры</span>
                            <span>→</span>
                        </button>
                        <button id="resetFilters" class="reset-btn">
                            <span>🗑️ Сбросить</span>
                        </button>
                    </div>
                </div>
                
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="eventType">🎭 Тип мероприятия</label>
                        <select id="eventType" class="filter-select">
                            <option value="">Любое мероприятие</option>
                            <option value="concert">Концерт</option>
                            <option value="festival">Фестиваль</option>
                            <option value="acoustic">Акустическое выступление</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="audienceSize">👥 Количество гостей</label>
                        <select id="audienceSize" class="filter-select">
                            <option value="">Любое количество</option>
                            <option value="small">До 50 человек</option>
                            <option value="medium">50-200 человек</option>
                            <option value="large">200+ человек</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="month">📅 Месяц проведения</label>
                        <select id="month" class="filter-select">
                            <?php
                            $months = [
                                'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                            ];
                            $currentMonth = $months[date('n') - 1];
                            foreach ($months as $index => $month) {
                                $selected = ($month === $currentMonth) ? 'selected' : '';
                                echo "<option value='{$month}' {$selected}>{$month}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div id="currentSelection" class="current-selection">
                    <div class="selection-title">Текущий выбор:</div>
                    <div class="selection-text" id="selectionText">
                        Месяц: <?php echo $currentMonth; ?> • Тип мероприятия: Любой • Гостей: Любое количество
                    </div>
                </div>
            </div>
            
            <!-- Умные рекомендации -->
            <div id="topChoiceCard" class="recommendation-card card-top-choice">
                <div class="card-header">
                    <div class="card-icon">🎯</div>
                    <div>
                        <h2 class="card-title">Топ выбора на <span id="currentMonthTitle"><?php echo $currentMonth; ?></span></h2>
                        <p class="card-subtitle" id="currentMonthSubtitle">
                            На основе анализа бронирований и отзывов пользователей
                        </p>
                    </div>
                </div>
                
                <div id="topVenuesContainer">
                    <div class="venue-recommendations">
                        <?php
                        $currentMonthData = $monthRecommendations[$currentMonth];
                        foreach ($currentMonthData['top_venues'] as $venue):
                        ?>
                        <div class="venue-recommendation">
                            <span class="recommendation-badge <?php echo $venue['badge']; ?>">
                                <?php echo $venue['badgeText']; ?>
                            </span>
                            <h3 style="margin-bottom: 10px; color: #2c3e50;"><?php echo $venue['name']; ?></h3>
                            <p style="color: #7f8c8d; margin-bottom: 8px;">📍 <?php echo $venue['district']; ?></p>
                            <p style="font-size: 0.95em; line-height: 1.5;"><?php echo $venue['reason']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Совет по формату -->
            <div id="formatAdviceCard" class="recommendation-card card-format-advice">
                <div class="card-header">
                    <div class="card-icon">💡</div>
                    <div>
                        <h2 class="card-title">Совет по формату</h2>
                        <p class="card-subtitle">Персональные рекомендации для вашего мероприятия</p>
                    </div>
                </div>
                
                <div id="formatAdviceContent">
                    <p style="font-size: 1.2em; line-height: 1.6; margin-bottom: 25px;">
                        <?php echo $currentMonthData['advice']; ?>
                    </p>
                    
                    <div class="ai-suggestion">
                        <h3>🤖 ИИ-совет:</h3>
                        <p style="font-size: 1.1em; line-height: 1.5;">
                            На основе вашего запроса система рекомендует площадки в популярных районах с хорошей транспортной доступностью. 
                            Учитывая выбранный месяц, обратите внимание на температурный режим и наличие укрытия от непогоды.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Срочность бронирования -->
            <div id="urgencyCard" class="recommendation-card card-urgency">
                <div class="card-header">
                    <div class="card-icon">⚡</div>
                    <div>
                        <h2 class="card-title">Срочность бронирования</h2>
                        <p class="card-subtitle">Рекомендации по срокам бронирования на <span id="urgencyMonth"><?php echo $currentMonth; ?></span></p>
                    </div>
                </div>
                
                <div id="urgencyContent">
                    <div class="booking-timeline">
                        <div class="timeline-header">
                            <h3>📅 График загруженности</h3>
                            <div class="timeline-month">Месяц: <?php echo $currentMonth; ?></div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-date">Сейчас</div>
                            <div class="timeline-content">
                                <span class="timeline-status status-busy">Занято</span>
                                <p>Высокая загруженность - мест почти нет</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-date">1-2 недели</div>
                            <div class="timeline-content">
                                <span class="timeline-status status-moderate">Ограничено</span>
                                <p>Средняя загруженность - рекомендуется бронирование</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-date">3-4 недели</div>
                            <div class="timeline-content">
                                <span class="timeline-status status-available">Свободно</span>
                                <p>Много свободных вариантов - лучшее время для брони</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-date">1+ месяц</div>
                            <div class="timeline-content">
                                <span class="timeline-status status-available">Свободно</span>
                                <p>Полная доступность - можно выбирать лучшие варианты</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="background: #f8d7da; padding: 20px; border-radius: 10px; margin-top: 20px;">
                        <p style="margin: 0; color: #721c24; font-weight: bold; font-size: 1.1em;">
                            ⚠️ <?php echo $currentMonthData['urgency']; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Навигация -->
            <div class="nav-buttons">
                <a href="index.php" class="nav-btn">
                    ← На главную
                </a>
                <a href="search.php" class="nav-btn secondary">
                    🔍 Расширенный поиск
                </a>
                <a href="venue_detail.php?id=1" class="nav-btn">
                    🏛️ Пример площадки
                </a>
            </div>
        </div>
    </main>
    
<?php require_once 'footer.php'; ?>

    <!-- JavaScript для динамических рекомендаций -->
    <script>
        // Данные рекомендаций по месяцам
        const monthRecommendations = <?php echo json_encode($monthRecommendations, JSON_UNESCAPED_UNICODE); ?>;
        
        // Сохраняем изначальное состояние (значения по умолчанию)
        const defaultMonth = '<?php echo $currentMonth; ?>';
        const defaultEventType = '';
        const defaultAudienceSize = '';
        
        document.addEventListener('DOMContentLoaded', function() {
            const applyBtn = document.getElementById('applyFilters');
            const resetBtn = document.getElementById('resetFilters');
            const eventTypeSelect = document.getElementById('eventType');
            const audienceSizeSelect = document.getElementById('audienceSize');
            const monthSelect = document.getElementById('month');
            const selectionText = document.getElementById('selectionText');
            const currentMonthTitle = document.getElementById('currentMonthTitle');
            const currentMonthSubtitle = document.getElementById('currentMonthSubtitle');
            const urgencyMonth = document.getElementById('urgencyMonth');
            
            // Инициализация
            updateSelectionText();
            
            // Обработчик нажатия кнопки "Применить фильтры"
            applyBtn.addEventListener('click', function() {
                applyRecommendations();
            });
            
            // Обработчик нажатия кнопки "Сбросить фильтры"
            resetBtn.addEventListener('click', function() {
                resetFilters();
            });
            
            // Автоматическое обновление при изменении месяца
            monthSelect.addEventListener('change', function() {
                updateSelectionText();
            });
            
            // Функция сброса фильтров
            function resetFilters() {
                
                
                // Сбрасываем значения в селектах
                eventTypeSelect.value = defaultEventType;
                audienceSizeSelect.value = defaultAudienceSize;
                monthSelect.value = defaultMonth;
                
                // Обновляем текст текущего выбора
                updateSelectionText();
                
                // Применяем рекомендации с дефолтными значениями
                currentMonthTitle.textContent = defaultMonth;
                currentMonthSubtitle.textContent = 'На основе анализа бронирований и отзывов пользователей';
                urgencyMonth.textContent = defaultMonth;
                
                // Обновляем рекомендации на основе месяца
                updateMonthRecommendations(defaultMonth);
                
                // Сбрасываем совет
                updateEventTypeAdvice('');
                
                // Обновляем срочность на основе месяца
                updateUrgencyInfo(defaultMonth);
                
                // Показываем уведомление
                showNotification('Фильтры сброшены!', 'info');
                
                // Скрываем загрузку
                setTimeout(() => {
                    applyBtn.innerHTML = '<span>Применить фильтры</span><span>→</span>';
                    applyBtn.disabled = false;
                    resetBtn.disabled = false;
                }, 500);
                
                // Прокручиваем к первой карточке
                document.getElementById('topChoiceCard').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            
            // Функция обновления текста текущего выбора
            function updateSelectionText() {
                const eventType = eventTypeSelect.value ? getOptionText(eventTypeSelect) : 'Любой';
                const audienceSize = audienceSizeSelect.value ? getOptionText(audienceSizeSelect) : 'Любое количество';
                const month = getOptionText(monthSelect);
                
                selectionText.innerHTML = `
                    Месяц: <strong>${month}</strong> • 
                    Тип мероприятия: <strong>${eventType}</strong> • 
                    Гостей: <strong>${audienceSize}</strong>
                `;
            }
            
            // Функция получения текста выбранной опции
            function getOptionText(selectElement) {
                return selectElement.options[selectElement.selectedIndex].text;
            }
            
            // Основная функция применения рекомендаций
            function applyRecommendations() {
                
                
                // Получаем значения фильтров
                const selectedMonth = monthSelect.value;
                const eventType = eventTypeSelect.value;
                const audienceSize = audienceSizeSelect.value;
                
                // Обновляем заголовки
                currentMonthTitle.textContent = selectedMonth;
                currentMonthSubtitle.textContent = generateSubtitle(eventType, audienceSize);
                urgencyMonth.textContent = selectedMonth;
                
                // Обновляем рекомендации на основе месяца
                updateMonthRecommendations(selectedMonth);
                
                // Обновляем советы на основе типа мероприятия
                updateEventTypeAdvice(eventType);
                
                // Обновляем срочность на основе месяца
                updateUrgencyInfo(selectedMonth);
                
                // Показываем уведомление
                showNotification('Фильтры успешно применены!', 'success');
                
                // Скрываем загрузку
                setTimeout(() => {
                    applyBtn.innerHTML = '<span>Применить фильтры</span><span>→</span>';
                    applyBtn.disabled = false;
                    resetBtn.disabled = false;
                }, 500);
                
                // Прокручиваем к первой карточке
                document.getElementById('topChoiceCard').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            
            // Функция обновления рекомендаций по месяцам
            function updateMonthRecommendations(month) {
                const monthData = monthRecommendations[month];
                
                if (!monthData) {
                    showNoResults();
                    return;
                }
                
                // Обновляем топ площадки
                const topVenuesContainer = document.getElementById('topVenuesContainer');
                const venuesHTML = monthData.top_venues.map(venue => `
                    <div class="venue-recommendation">
                        <span class="recommendation-badge ${venue.badge}">
                            ${venue.badgeText}
                        </span>
                        <h3 style="margin-bottom: 10px; color: #2c3e50;">${venue.name}</h3>
                        <p style="color: #7f8c8d; margin-bottom: 8px;">📍 ${venue.district}</p>
                        <p style="font-size: 0.95em; line-height: 1.5;">${venue.reason}</p>
                    </div>
                `).join('');
                
                topVenuesContainer.innerHTML = `
                    <div class="venue-recommendations">
                        ${venuesHTML}
                    </div>
                `;
                
                // Обновляем совет
                const formatAdviceContent = document.getElementById('formatAdviceContent');
                formatAdviceContent.innerHTML = `
                    <p style="font-size: 1.2em; line-height: 1.6; margin-bottom: 25px;">
                        ${monthData.advice}
                    </p>
                    
                    <div class="ai-suggestion">
                        <h3>🤖 ИИ-совет:</h3>
                        <p style="font-size: 1.1em; line-height: 1.5;">
                            На основе анализа данных за ${month}, система рекомендует площадки 
                            с учетом сезонных особенностей. ${generateAITip(month)}
                        </p>
                    </div>
                `;
                
                // Обновляем срочность
                const urgencyContent = document.getElementById('urgencyContent');
                if (urgencyContent) {
                    urgencyContent.querySelector('.timeline-month').textContent = `Месяц: ${month}`;
                    urgencyContent.querySelector('.timeline-header + div').innerHTML = `
                        <p style="margin: 0; color: #721c24; font-weight: bold; font-size: 1.1em;">
                            ⚠️ ${monthData.urgency}
                        </p>
                    `;
                }
            }
            
            // Функция обновления советов по типу мероприятия
            function updateEventTypeAdvice(eventType) {
                const aiSuggestion = document.querySelector('.ai-suggestion p');
                if (!aiSuggestion) return;
                
                const eventTypeTips = {
                    'concert': 'Для концертов рекомендуем площадки с хорошей акустикой и профессиональным оборудованием.',
                    'festival': 'Для фестивалей выбирайте открытые площадки с большой территорией и инфраструктурой.',
                    'acoustic': 'Для акустических выступлений подойдут камерные залы с хорошей атмосферой.',
                };
                
                if (eventType && eventTypeTips[eventType]) {
                    aiSuggestion.innerHTML = eventTypeTips[eventType] + ' На основе анализа данных за выбранный месяц, система рекомендует площадки с учетом сезонных особенностей.';
                } else {
                    // Возвращаем дефолтный текст
                    aiSuggestion.innerHTML = 'На основе вашего запроса система рекомендует площадки в популярных районах с хорошей транспортной доступностью. На основе анализа данных за выбранный месяц, система рекомендует площадки с учетом сезонных особенностей.';
                }
            }
            
            // Функция обновления информации о срочности
            function updateUrgencyInfo(month) {
                const timelineItems = document.querySelectorAll('.timeline-item');
                
                // Обновляем статусы в зависимости от месяца
                const monthFactors = {
                    'Январь': { busy: 3, moderate: 2, available: 1 },
                    'Февраль': { busy: 2, moderate: 3, available: 1 },
                    'Март': { busy: 2, moderate: 2, available: 2 },
                    'Апрель': { busy: 4, moderate: 2, available: 1 },
                    'Май': { busy: 5, moderate: 2, available: 1 },
                    'Июнь': { busy: 4, moderate: 3, available: 1 },
                    'Июль': { busy: 3, moderate: 3, available: 2 },
                    'Август': { busy: 4, moderate: 3, available: 1 },
                    'Сентябрь': { busy: 3, moderate: 3, available: 2 },
                    'Октябрь': { busy: 2, moderate: 2, available: 2 },
                    'Ноябрь': { busy: 1, moderate: 2, available: 3 },
                    'Декабрь': { busy: 5, moderate: 2, available: 1 }
                };
                
                const factor = monthFactors[month] || { busy: 2, moderate: 2, available: 2 };
                
                timelineItems.forEach((item, index) => {
                    const statusSpan = item.querySelector('.timeline-status');
                    const statusText = item.querySelector('.timeline-content p');
                    
                    if (index < factor.busy) {
                        statusSpan.className = 'timeline-status status-busy';
                        statusSpan.textContent = 'Занято';
                        statusText.textContent = 'Высокая загруженность - бронировать немедленно';
                    } else if (index < factor.busy + factor.moderate) {
                        statusSpan.className = 'timeline-status status-moderate';
                        statusSpan.textContent = 'Ограничено';
                        statusText.textContent = 'Средняя загруженность - рекомендуется бронирование';
                    } else {
                        statusSpan.className = 'timeline-status status-available';
                        statusSpan.textContent = 'Свободно';
                        statusText.textContent = 'Много свободных вариантов - можно выбирать лучшие';
                    }
                });
            }
            
            // Генерация подзаголовка
            function generateSubtitle(eventType, audienceSize) {
                const parts = [];
                
                if (eventType) parts.push(`для ${getOptionText(eventTypeSelect).toLowerCase()}`);
                if (audienceSize) parts.push(`${getOptionText(audienceSizeSelect).toLowerCase()} гостей`);
                
                return parts.length > 0 
                    ? `Рекомендации ${parts.join(', ')}` 
                    : 'На основе анализа бронирований и отзывов пользователей';
            }
            
            // Генерация ИИ-совета
            function generateAITip(month) {
                const tips = {
                    'Январь': 'Обратите внимание на отопление и удобный подъезд в зимних условиях.',
                    'Февраль': 'Учитывайте возможные снегопады и выбирайте площадки с крытыми переходами.',
                    'Март': 'Весенняя распутица может создать сложности с парковкой - выбирайте площадки с твердым покрытием.',
                    'Апрель': 'Идеальное время для открытых площадок, но имейте запасной вариант на случай дождя.',
                    'Май': 'Пик сезона - бронируйте заранее и уточняйте условия отмены при непогоде.',
                    'Июнь': 'Длинные световые дни позволяют проводить вечерние мероприятия при естественном освещении.',
                    'Июль': 'Выбирайте площадки с навесами от солнца и хорошей вентиляцией.',
                    'Август': 'Идеально для ночных мероприятий - теплые ночи и звездное небо.',
                    'Сентябрь': 'Бархатный сезон - комфортная температура и красивые осенние пейзажи.',
                    'Октябрь': 'Имейте запасной план на случай дождя, выбирайте площадки с навесами.',
                    'Ноябрь': 'Переходите на закрытые площадки с отоплением и хорошим освещением.',
                    'Декабрь': 'Новогодний антураж создает особую атмосферу, но цены могут быть выше.'
                };
                
                return tips[month] || 'Рекомендуем уточнять условия площадки заранее.';
            }
            
            // Показать уведомление
            function showNotification(message, type = 'success') {
                // Удаляем старое уведомление
                const oldNotification = document.querySelector('.notification');
                if (oldNotification) {
                    oldNotification.remove();
                }
                
                // Цвета для разных типов уведомлений
                const colors = {
                    'success': '#2ecc71',
                    'error': '#e74c3c',
                    'info': '#3498db',
                    'warning': '#f39c12'
                };
                
                const icons = {
                    'success': '✅',
                    'error': '❌',
                    'info': 'ℹ️',
                    'warning': '⚠️'
                };
                
                // Создаем новое уведомление
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.innerHTML = `
                    <div style="
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 15px 25px;
                        background: ${colors[type] || colors['success']};
                        color: white;
                        border-radius: 10px;
                        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                        z-index: 10000;
                        animation: slideInRight 0.3s ease-out;
                        display: flex;
                        align-items: center;
                        gap: 15px;
                    ">
                        <span style="font-size: 1.3em;">${icons[type] || icons['success']}</span>
                        <span>${message}</span>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Автоматическое скрытие через 3 секунды
                setTimeout(() => {
                    notification.classList.add('hidden');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
            
            // Показать сообщение об отсутствии результатов
            function showNoResults() {
                const topVenuesContainer = document.getElementById('topVenuesContainer');
                topVenuesContainer.innerHTML = `
                    <div class="no-results">
                        <p style="font-size: 1.5em; margin-bottom: 15px;">😕</p>
                        <p>Нет данных для выбранного месяца</p>
                        <p style="font-size: 0.9em; margin-top: 10px;">Попробуйте выбрать другой месяц или настройки</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
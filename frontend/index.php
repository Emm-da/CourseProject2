<?php
// index.php - Главная страница с картой и статистикой
header('Content-Type: text/html; charset=utf-8');
require_once 'header.php'; // Подключаем общую шапку

// Подключаемся к базе данных
$host = 'localhost';
$dbname = 'music_venues_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Берем ВСЕ площадки
    $stmt = $pdo->query("
        SELECT 
            id,
            name,
            district,
            address,
            working_hours,
            balance_holder_phone,
            geo_data
        FROM venues 
        WHERE geo_data IS NOT NULL
        AND geo_data != ''
    ");
    
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Собираем данные для карты
    $mapData = [];
    $venueTypes = []; // Для сбора уникальных типов
    foreach ($venues as $venue) {
        // Парсим координаты из geo_data
        $geo_data = $venue['geo_data'];
        $lat = 0;
        $lng = 0;
        
        // Ищем координаты в формате: {coordinates=[37.673708995, 55.795444616], type=Point}
        if (preg_match('/coordinates=\[([\d\.]+),\s*([\d\.]+)\]/', $geo_data, $matches)) {
            $lng = floatval($matches[1]); // первое число - долгота
            $lat = floatval($matches[2]); // второе число - широта
        }
        
        // Определяем тип площадки по названию
        $venueType = 'other';
        $lowerName = strtolower($venue['name']);
        if (strpos($lowerName, 'эстрада') !== false) {
            $venueType = 'estrada';
        } elseif (strpos($lowerName, 'сцена') !== false) {
            $venueType = 'stage';
        } elseif (strpos($lowerName, 'парк') !== false) {
            $venueType = 'park';
        }
        
        // Сохраняем тип для фильтра
        if (!in_array($venueType, $venueTypes)) {
            $venueTypes[] = $venueType;
        }
        
        // Если нашли координаты - добавляем
        if ($lat != 0 && $lng != 0) {
            $mapData[] = [
                'id' => $venue['id'],
                'name' => $venue['name'],
                'district' => $venue['district'],
                'address' => $venue['address'],
                'lat' => $lat,
                'lng' => $lng,
                'workingHours' => $venue['working_hours'],
                'phone' => $venue['balance_holder_phone'],
                'type' => $venueType
            ];
        }
    }
    
    // Сортируем типы
    sort($venueTypes);
    
    // Получаем статистику
    $totalVenues = $pdo->query("SELECT COUNT(*) FROM venues")->fetchColumn();
    $uniqueDistricts = $pdo->query("SELECT COUNT(DISTINCT district) FROM venues WHERE district IS NOT NULL")->fetchColumn();
    $withCoords = count($mapData);
    
    // Получаем рекомендации (топ 3 площадки)
    $recommendationsStmt = $pdo->query("
        SELECT id, name, district, address, working_hours, balance_holder_phone 
        FROM venues 
        WHERE district IS NOT NULL 
        LIMIT 3
    ");
    $recommendations = $recommendationsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем список районов
    $districtsStmt = $pdo->query("SELECT DISTINCT district FROM venues WHERE district IS NOT NULL ORDER BY district");
    $districts = $districtsStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Стили только для главной страницы -->
<style>
    body {
        margin: 0;
        padding: 0;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
        border-radius: 0 0 30px 30px;
        margin-bottom: 30px;
        margin-top: 0;
    }
    
    .hero-title {
        font-size: 3em;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .hero-subtitle {
        font-size: 1.2em;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto 30px;
    }
    
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .stat-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .stat-number {
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .map-container {
        height: 600px;
        border-radius: 15px;
        overflow: hidden;
        margin: 40px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border: 3px solid #4a6fa5;
        position: relative;
    }
    
    #map {
        width: 100%;
        height: 100%;
    }
    
    .section-title {
        color: #2c3e50;
        margin: 40px 0 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #4a6fa5;
        font-size: 1.8em;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }
    
    .feature-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
        border-top: 5px solid #4a6fa5;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .feature-icon {
        font-size: 2.5em;
        margin-bottom: 15px;
        color: #4a6fa5;
    }
    
    .quick-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin: 30px 0;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 12px 25px;
        background: #4a6fa5;
        color: white;
        text-decoration: none;
        border-radius: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .action-btn:hover {
        background: #3a5a80;
        transform: translateY(-2px);
    }
    
    .action-btn.secondary {
        background: #7f8c8d;
    }
    
    .action-btn.secondary:hover {
        background: #6c7b7d;
    }
    
    .map-controls {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 300px;
    }
    
    .map-search {
        margin-bottom: 0;
    }
    
    .search-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 25px;
        font-size: 14px;
        box-sizing: border-box;
    }
    
    .filters-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .filter-select {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        flex: 1;
        min-width: 120px;
    }
    
    .map-legend {
        position: absolute;
        bottom: 20px;
        left: 20px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        max-width: 250px;
    }
    
    .legend-title {
        font-weight: bold;
        margin-bottom: 10px;
        color: #2c3e50;
        font-size: 14px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 12px;
    }
    
    .legend-color {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-right: 8px;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .venues-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }
    
    .venue-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    
    .venue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .venue-name {
        color: #2c3e50;
        font-size: 1.4em;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .venue-info {
        color: #555;
        margin-bottom: 8px;
        font-size: 0.95em;
    }
    
    .venue-info strong {
        color: #4a6fa5;
        font-weight: 600;
    }
    
    .info-box {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 15px;
        margin: 30px 0;
        border-left: 5px solid #4a6fa5;
    }
    
    .info-box h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.5em;
    }
    
    .info-box p {
        color: #555;
        line-height: 1.6;
        font-size: 1.1em;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 40px 20px;
        }
        
        .hero-title {
            font-size: 2.2em;
        }
        
        .hero-subtitle {
            font-size: 1.1em;
        }
        
        .quick-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .map-container {
            height: 400px;
            margin: 20px 0;
        }
        
        .map-controls {
            position: relative;
            top: 0;
            right: 0;
            margin: 10px;
            max-width: 100%;
        }
        
        .map-legend {
            position: relative;
            bottom: 0;
            left: 0;
            margin: 10px;
            max-width: 100%;
        }
        
        .filters-row {
            flex-direction: column;
        }
        
        .search-input {
            width: 100%;
        }
        
        .filter-select {
            min-width: 100%;
        }
        
        .quick-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .action-btn {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
        
        .features-grid,
        .venues-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .section-title {
            font-size: 1.5em;
        }
    }
</style>

</head>
<body>
    <!-- Шапка уже подключена через header.php -->
    
    <main>
        <!-- Герой-секция -->
        <section class="hero-section">
            <div class="container">
                <h1 class="hero-title">🎵 Музыкальные площадки Москвы</h1>
                <p class="hero-subtitle">Найдите идеальную площадку для вашего мероприятия среди <?php echo $totalVenues; ?>+ локаций в парках Москвы</p>
                
                <!-- Быстрая статистика -->
                <div class="quick-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $totalVenues; ?>+</div>
                        <div>площадок</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $uniqueDistricts; ?></div>
                        <div>районов</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $withCoords; ?></div>
                        <div>на карте</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div>поиск</div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="container">
            <!-- Быстрые действия -->
            <div class="quick-actions">
                <a href="search.php" class="action-btn">
                    🔍 Поиск площадок
                </a>
                <a href="#map-section" class="action-btn secondary">
                    🗺️ Посмотреть карту
                </a>
                <a href="recommendations.php" class="action-btn">
                    💡 Получить рекомендации
                </a>
                <a href="about.php" class="action-btn">
                    ℹ️ О проекте
                </a>
            </div>
            
            <!-- Карта -->
            <section id="map-section">
                <h2 class="section-title">🗺️ Карта музыкальных площадок Москвы</h2>
                <div class="map-container">
                    <div id="map"></div>
                    <!-- Элементы управления картой -->
                    <div class="map-controls">
                        <div class="map-search">
                            <input type="text" id="mapSearch" class="search-input" placeholder="Поиск на карте...">
                        </div>
                        <div class="filters-row">
                            <select id="districtFilter" class="filter-select">
                                <option value="">Все районы</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?php echo htmlspecialchars($district); ?>">
                                        <?php echo htmlspecialchars($district); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select id="typeFilter" class="filter-select">
                                <option value="">Все типы</option>
                                <option value="estrada">Эстрады</option>
                                <option value="stage">Сцены</option>
                                <option value="park">Парки</option>
                                <option value="other">Остальные</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Легенда карты -->
                    <div class="map-legend">
                        <div class="legend-title">Легенда карты:</div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #e74c3c;"></div>
                            <span>Эстрады</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #2ecc71;"></div>
                            <span>Сцены</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #3498db;"></div>
                            <span>Парки</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #9b59b6;"></div>
                            <span>Остальные площадки</span>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Функционал сайта -->
            <section>
                <h2 class="section-title">✨ Что вы можете сделать</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🔍</div>
                        <h3>Искать площадки</h3>
                        <p>По названию, району, округу или адресу. Используйте фильтры для точного поиска.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🗺️</div>
                        <h3>Смотреть на карте</h3>
                        <p>Найдите площадки на интерактивной карте Москвы с удобной навигацией.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💡</div>
                        <h3>Получать рекомендации</h3>
                        <p>Умная система подберет идеальную площадку по вашим параметрам.</p>
                    </div>
                </div>
            </section>
            
            <!-- Рекомендации -->
            <?php if (!empty($recommendations)): ?>
            <section>
                <h2 class="section-title">🔥 Популярные площадки</h2>
                <div class="venues-grid">
                    <?php foreach ($recommendations as $venue): ?>
                    <div class="venue-card">
                        <h3 class="venue-name"><?php echo htmlspecialchars($venue['name']); ?></h3>
                        <?php if ($venue['district']): ?>
                            <p class="venue-info">📍 <?php echo htmlspecialchars($venue['district']); ?></p>
                        <?php endif; ?>
                        <?php if ($venue['address']): ?>
                            <p class="venue-info">🏛️ <?php echo htmlspecialchars($venue['address']); ?></p>
                        <?php endif; ?>
                        <?php if ($venue['balance_holder_phone']): ?>
                            <p class="venue-info">📞 <?php echo htmlspecialchars($venue['balance_holder_phone']); ?></p>
                        <?php endif; ?>
                        <?php if ($venue['id']): ?>
                            <a href="venue_detail.php?id=<?php echo $venue['id']; ?>" class="action-btn" style="margin-top: 15px; display: inline-block;">
                                Подробнее →
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Информация о данных -->
            <div class="info-box">
                <h3>📊 О данных</h3>
                <p>Используются открытые данные Правительства Москвы о музыкальных площадках в парках города. Всего в базе более <?php echo $totalVenues; ?> площадок с информацией о местоположении, графике работы и контактах.</p>
            </div>
        </div>
    </main>
    
<?php require_once 'footer.php'; ?>

    <!-- JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Данные из PHP
        const venuesData = <?php echo json_encode($mapData, JSON_UNESCAPED_UNICODE); ?>;
        let currentMarkers = [];
        
        // Инициализация карты
        function initMap() {
            // Москва: широта 55.7558, долгота 37.6173
            const map = L.map('map').setView([55.7558, 37.6173], 11);
            
            // Добавляем слой карты
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Загружаем данные о площадках
            loadVenuesToMap(map);
            
            // Поиск на карте
            document.getElementById('mapSearch').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchOnMap(this.value, map);
                }
            });
            
            // Фильтр по району
            document.getElementById('districtFilter').addEventListener('change', function() {
                applyFilters(map);
            });
            
            // Фильтр по типу
            document.getElementById('typeFilter').addEventListener('change', function() {
                applyFilters(map);
            });
        }
        
        // Цвета для разных типов площадок
        const getVenueColor = (venueType) => {
            const colors = {
                'estrada': '#e74c3c',   // Красный - эстрады
                'stage': '#2ecc71',     // Зеленый - сцены
                'park': '#3498db',      // Синий - парки
                'other': '#9b59b6'      // Фиолетовый - остальные
            };
            return colors[venueType] || '#9b59b6';
        };
        
        // Русские названия типов
        const getVenueTypeName = (venueType) => {
            const names = {
                'estrada': 'Эстрада',
                'stage': 'Сцена',
                'park': 'Парк',
                'other': 'Другое'
            };
            return names[venueType] || 'Другое';
        };
        
        // Загрузка площадок на карту
        function loadVenuesToMap(map, filters = {}) {
            // Очищаем старые маркеры
            currentMarkers.forEach(marker => map.removeLayer(marker));
            currentMarkers = [];
            
            // Фильтруем данные если нужно
            let filteredData = venuesData;
            
            // Применяем фильтр по району
            if (filters.district) {
                filteredData = filteredData.filter(venue => 
                    venue.district && venue.district.toLowerCase() === filters.district.toLowerCase()
                );
            }
            
            // Применяем фильтр по типу
            if (filters.type) {
                filteredData = filteredData.filter(venue => venue.type === filters.type);
            }
            
            console.log('Отображаем площадок:', filteredData.length);
            
            // Добавляем маркеры
            filteredData.forEach(venue => {
                // Проверяем координаты
                if (!venue.lat || !venue.lng) {
                    console.warn('Нет координат у площадки:', venue.name);
                    return;
                }
                
                // Создаем иконку
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background: ${getVenueColor(venue.type)};
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 12px;
                    ">🎵</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                
                // Создаем маркер
                const marker = L.marker([venue.lat, venue.lng], { icon: icon })
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width: 250px;">
                            <h3 style="margin: 0 0 10px 0;">${venue.name}</h3>
                            <div style="display: inline-block; padding: 2px 8px; background: ${getVenueColor(venue.type)}; color: white; border-radius: 10px; font-size: 12px; margin-bottom: 10px;">
                                ${getVenueTypeName(venue.type)}
                            </div>
                            ${venue.address ? `<p style="margin: 5px 0;"><strong>📍 Адрес:</strong> ${venue.address}</p>` : ''}
                            ${venue.district ? `<p style="margin: 5px 0;"><strong>🏛️ Район:</strong> ${venue.district}</p>` : ''}
                            ${venue.workingHours ? `<p style="margin: 5px 0;"><strong>⏰ График:</strong> ${venue.workingHours}</p>` : ''}
                            ${venue.phone ? `<p style="margin: 5px 0;"><strong>📞 Телефон:</strong> ${venue.phone}</p>` : ''}
                            <div style="margin-top: 15px;">
                                <a href="venue_detail.php?id=${venue.id}" 
                                   style="padding: 8px 15px; background: #4a6fa5; color: white; 
                                          text-decoration: none; border-radius: 5px; display: inline-block;">
                                    Подробнее →
                                </a>
                            </div>
                        </div>
                    `);
                
                currentMarkers.push(marker);
            });
            
            // Если отфильтровали и есть результаты, центрируем карту
            if ((filters.district || filters.type) && filteredData.length > 0) {
                map.setView([filteredData[0].lat, filteredData[0].lng], 13);
            } else if (!filters.district && !filters.type) {
                // Возвращаемся к виду по умолчанию
                map.setView([55.7558, 37.6173], 11);
            }
        }
        
        // Применение всех фильтров
        function applyFilters(map) {
            const district = document.getElementById('districtFilter').value;
            const type = document.getElementById('typeFilter').value;
            
            loadVenuesToMap(map, {
                district: district,
                type: type
            });
        }
        
        // Поиск на карте
        function searchOnMap(query, map) {
            if (!query.trim()) return;
            
            // Ищем в названии, адресе или районе
            const foundVenue = venuesData.find(venue => {
                const searchText = (venue.name + ' ' + venue.address + ' ' + venue.district).toLowerCase();
                return searchText.includes(query.toLowerCase());
            });
            
            if (foundVenue) {
                map.setView([foundVenue.lat, foundVenue.lng], 15);
                
                // Подсвечиваем найденную площадку
                L.circle([foundVenue.lat, foundVenue.lng], {
                    color: '#f39c12',
                    fillColor: '#f1c40f',
                    fillOpacity: 0.2,
                    radius: 200
                }).addTo(map).bindPopup(`<b>Найдено:</b> ${foundVenue.name}`).openPopup();
            } else {
                alert('Площадка не найдена');
            }
        }
        
        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>
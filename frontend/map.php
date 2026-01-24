<?php
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost';
$dbname = 'music_venues_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$stmt = $pdo->query("
    SELECT 
        id,
        name,
        district,
        adm_area,
        address,
        latitude,
        longitude,
        working_hours,
        balance_holder_phone,
        wifi_availability
    FROM venues 
    WHERE latitude IS NOT NULL 
    AND longitude IS NOT NULL
    AND TRIM(latitude) != ''
    AND TRIM(longitude) != ''
    AND latitude != 0
    AND longitude != 0
    ORDER BY name
");

$venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Карта музыкальных площадок Москвы</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5;
        }
        
        .map-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 350px;
            background: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .map-container {
            flex: 1;
            position: relative;
        }
        
        #map {
            width: 100%;
            height: 100vh;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .stat-value {
            font-weight: bold;
            color: #4a6fa5;
        }
        
        .filters {
            margin-bottom: 20px;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 14px;
        }
        
        select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }
        
        select:focus {
            outline: none;
            border-color: #4a6fa5;
        }
        
        .controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 15px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3a5a80;
        }
        
        .btn-secondary {
            background: #7f8c8d;
        }
        
        .btn-secondary:hover {
            background: #6c7b7d;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #219653;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .venues-list {
            margin-top: 20px;
        }
        
        .venue-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .venue-item:hover {
            background: #f8f9fa;
        }
        
        .venue-name {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .venue-info {
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .map-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .search-box {
            display: flex;
            margin-bottom: 10px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px 0 0 8px;
            font-size: 14px;
        }
        
        .search-box button {
            padding: 10px 15px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }
        
        .legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 12px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
            border: 2px solid white;
        }
        
        .popup-content {
            max-width: 250px;
        }
        
        .popup-title {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .popup-info {
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .popup-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
        }
        
        .popup-btn {
            padding: 5px 10px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .map-wrapper {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: 300px;
            }
            
            #map {
                height: calc(100vh - 300px);
            }
        }
    </style>
</head>
<body>
    <div class="map-wrapper">
        <div class="sidebar">
            <div class="header">
                <h1>Карта площадок Москвы</h1>
                <p>Интерактивная карта музыкальных площадок</p>
            </div>
            
            <div class="stats">
                <div class="stat-item">
                    <span>Всего площадок:</span>
                    <span class="stat-value" id="totalVenues"><?php echo count($venues); ?></span>
                </div>
                <div class="stat-item">
                    <span>На карте:</span>
                    <span class="stat-value" id="visibleVenues">0</span>
                </div>
                <div class="stat-item">
                    <span>Выбрано:</span>
                    <span class="stat-value" id="selectedVenues">0</span>
                </div>
            </div>
            
            <div class="controls">
                <button onclick="zoomToMoscow()" class="btn">Вся Москва</button>
                <button onclick="showAllMarkers()" class="btn btn-success">Все площадки</button>
                <button onclick="getUserLocation()" class="btn">Моё место</button>
                <button onclick="clearMap()" class="btn btn-danger">Очистить</button>
            </div>
            
            <div class="filters">
                <div class="filter-group">
                    <label class="filter-label">Район:</label>
                    <select id="districtFilter" onchange="filterByDistrict()">
                        <option value="">Все районы</option>
                        <?php
                        $districtsStmt = $pdo->query("
                            SELECT DISTINCT district 
                            FROM venues 
                            WHERE district IS NOT NULL 
                            AND TRIM(district) != ''
                            AND latitude IS NOT NULL
                            ORDER BY district
                        ");
                        $districts = $districtsStmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        foreach ($districts as $district) {
                            echo '<option value="' . htmlspecialchars($district) . '">' . 
                                 htmlspecialchars($district) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Округ:</label>
                    <select id="areaFilter" onchange="filterByArea()">
                        <option value="">Все округа</option>
                        <?php
                        $areasStmt = $pdo->query("
                            SELECT DISTINCT adm_area 
                            FROM venues 
                            WHERE adm_area IS NOT NULL 
                            AND TRIM(adm_area) != ''
                            AND latitude IS NOT NULL
                            ORDER BY adm_area
                        ");
                        $areas = $areasStmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        foreach ($areas as $area) {
                            echo '<option value="' . htmlspecialchars($area) . '">' . 
                                 htmlspecialchars($area) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Поиск площадки...">
                <button onclick="searchVenue()">Поиск</button>
            </div>
            
            <div class="venues-list" id="venuesList">
            </div>
        </div>
        
        <div class="map-container">
            <div id="map"></div>
            
            <div class="map-controls">
                <div class="search-box">
                    <input type="text" id="mapSearch" placeholder="Поиск на карте...">
                    <button onclick="searchOnMap()">Найти</button>
                </div>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #e74c3c;"></div>
                    <span>Эстрада</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #3498db;"></div>
                    <span>Парк</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #2ecc71;"></div>
                    <span>Сцена</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f39c12;"></div>
                    <span>Другое</span>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let markers = [];
        let venuesData = <?php echo json_encode($venues, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>;
        let userMarker = null;
        
        function initMap() {
            console.log('Инициализация карты...');
            console.log('Данные площадок:', venuesData.length);
            
            map = L.map('map', {
                center: [55.7558, 37.6173],
                zoom: 11,
                zoomControl: true
            });
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);
            
            addMarkersToMap();
            
            updateStats();
            
            updateVenuesList();
            
            console.log('Карта инициализирована');
        }
        
        function addMarkersToMap() {
            console.log('Добавление маркеров...');
            
            clearMarkers();
            
            let markersAdded = 0;
            
            venuesData.forEach(venue => {
                if (venue.latitude && venue.longitude) {
                    const lat = parseFloat(venue.latitude);
                    const lng = parseFloat(venue.longitude);
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        addMarker(venue, lat, lng);
                        markersAdded++;
                    }
                }
            });
            
            console.log('Добавлено маркеров:', markersAdded);
            document.getElementById('visibleVenues').textContent = markersAdded;
        }
        
        function addMarker(venue, lat, lng) {
            const color = getVenueColor(venue.name);
            
            const icon = L.divIcon({
                className: 'venue-marker',
                html: `
                    <div style="
                        background: ${color};
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        border: 2px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 12px;
                        cursor: pointer;
                    ">
                        M
                    </div>
                `,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            
            const marker = L.marker([lat, lng], { icon: icon });
            
            marker.bindPopup(createPopupContent(venue));
            
            marker.addTo(map);
            
            markers.push({
                marker: marker,
                venue: venue
            });
            
            marker.on('click', function() {
                highlightVenue(venue.id);
            });
        }
        
        function getVenueColor(name) {
            const lowerName = (name || '').toLowerCase();
            
            if (lowerName.includes('эстрада')) return '#e74c3c';
            if (lowerName.includes('сцена')) return '#2ecc71';
            if (lowerName.includes('парк')) return '#3498db';
            if (lowerName.includes('террас')) return '#9b59b6';
            return '#f39c12';
        }
        
        function createPopupContent(venue) {
            const wifi = venue.wifi_availability ? '✓' : '✗';
            
            return `
                <div class="popup-content">
                    <div class="popup-title">${venue.name || 'Неизвестно'}</div>
                    <div class="popup-info"><strong>Адрес:</strong> ${venue.address || 'Не указан'}</div>
                    <div class="popup-info"><strong>Район:</strong> ${venue.district || 'Не указан'}</div>
                    ${venue.working_hours ? `<div class="popup-info"><strong>График:</strong> ${venue.working_hours}</div>` : ''}
                    <div class="popup-info"><strong>WiFi:</strong> ${wifi}</div>
                    <div class="popup-actions">
                        <button onclick="zoomToVenue(${venue.latitude}, ${venue.longitude})" class="popup-btn">
                            Приблизить
                        </button>
                        ${venue.id ? `
                        <a href="venue_detail.php?id=${venue.id}" class="popup-btn" style="background: #27ae60;">
                            Подробнее
                        </a>
                        ` : ''}
                    </div>
                </div>
            `;
        }
        
        function clearMarkers() {
            markers.forEach(item => {
                map.removeLayer(item.marker);
            });
            markers = [];
        }
        
        function zoomToMoscow() {
            map.setView([55.7558, 37.6173], 11);
            showNotification('Карта центрирована на Москве');
        }
        
        function showAllMarkers() {
            if (markers.length > 0) {
                const bounds = L.latLngBounds(markers.map(item => item.marker.getLatLng()));
                map.fitBounds(bounds, { padding: [50, 50] });
                showNotification('Показаны все площадки');
            }
        }
        
        function zoomToVenue(lat, lng) {
            map.setView([lat, lng], 16);
        }
        
        function clearMap() {
            clearMarkers();
            if (userMarker) {
                map.removeLayer(userMarker);
                userMarker = null;
            }
            updateStats();
            showNotification('Карта очищена');
        }
        
        function filterByDistrict() {
            const district = document.getElementById('districtFilter').value;
            filterVenues('district', district);
        }
        
        function filterByArea() {
            const area = document.getElementById('areaFilter').value;
            filterVenues('adm_area', area);
        }
        
        function filterVenues(field, value) {
            if (!value) {
                markers.forEach(item => {
                    item.marker.addTo(map);
                });
            } else {
                markers.forEach(item => {
                    if (item.venue[field] && item.venue[field].includes(value)) {
                        item.marker.addTo(map);
                    } else {
                        map.removeLayer(item.marker);
                    }
                });
            }
            
            updateStats();
            updateVenuesList();
        }
        
        function searchVenue() {
            const query = document.getElementById('searchInput').value.trim().toLowerCase();
            if (!query) return;
            
            const found = markers.filter(item => {
                const venue = item.venue;
                return (
                    (venue.name && venue.name.toLowerCase().includes(query)) ||
                    (venue.address && venue.address.toLowerCase().includes(query)) ||
                    (venue.district && venue.district.toLowerCase().includes(query))
                );
            });
            
            if (found.length > 0) {
                markers.forEach(item => {
                    map.removeLayer(item.marker);
                });
                
                found.forEach(item => {
                    item.marker.addTo(map);
                });
                
                const firstMarker = found[0].marker;
                map.setView(firstMarker.getLatLng(), 15);
                firstMarker.openPopup();
                
                showNotification(`Найдено площадок: ${found.length}`);
            } else {
                showNotification('Ничего не найдено', 'error');
            }
            
            updateStats();
            updateVenuesList();
        }
        
        function searchOnMap() {
            const query = document.getElementById('mapSearch').value.trim();
            if (!query) return;
            
            showNotification('Поиск на карте: ' + query);
        }
        
        function getUserLocation() {
            if (!navigator.geolocation) {
                showNotification('Геолокация не поддерживается', 'error');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    
                    userMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'user-location',
                            html: '📍',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30]
                        })
                    }).addTo(map);
                    
                    userMarker.bindPopup('Ваше местоположение').openPopup();
                    map.setView([lat, lng], 14);
                    
                    showNotification('Ваше местоположение определено');
                },
                error => {
                    showNotification('Не удалось определить местоположение', 'error');
                }
            );
        }
        
        function updateStats() {
            const visible = markers.filter(item => map.hasLayer(item.marker)).length;
            document.getElementById('visibleVenues').textContent = visible;
        }
        
        function updateVenuesList() {
            const list = document.getElementById('venuesList');
            const visibleMarkers = markers.filter(item => map.hasLayer(item.marker));
            
            list.innerHTML = '';
            
            visibleMarkers.forEach(item => {
                const venue = item.venue;
                const div = document.createElement('div');
                div.className = 'venue-item';
                div.onclick = () => {
                    item.marker.openPopup();
                    map.setView(item.marker.getLatLng(), 16);
                };
                
                div.innerHTML = `
                    <div class="venue-name">${venue.name || 'Неизвестно'}</div>
                    <div class="venue-info">${venue.district || ''} • ${venue.address ? venue.address.substring(0, 30) + '...' : ''}</div>
                `;
                
                list.appendChild(div);
            });
            
            document.getElementById('selectedVenues').textContent = visibleMarkers.length;
        }
        
        function highlightVenue(venueId) {
            markers.forEach(item => {
                const icon = item.marker.getIcon();
                if (icon.options.html.includes('border: 3px solid yellow')) {
                    icon.options.html = icon.options.html.replace('border: 3px solid yellow', 'border: 2px solid white');
                    item.marker.setIcon(icon);
                }
            });
            
            const selected = markers.find(item => item.venue.id == venueId);
            if (selected) {
                const icon = selected.marker.getIcon();
                icon.options.html = icon.options.html.replace('border: 2px solid white', 'border: 3px solid yellow');
                selected.marker.setIcon(icon);
            }
        }
        
        function showNotification(message, type = 'success') {
            const old = document.querySelector('.notification');
            if (old) old.remove();
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.style.background = type === 'error' ? '#ffebee' : '#e8f5e9';
            notification.style.borderLeft = type === 'error' ? '4px solid #f44336' : '4px solid #4caf50';
            
            notification.innerHTML = `
                <span style="font-size: 1.2em;">
                    ${type === 'error' ? 'X' : 'V'}
                </span>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }
        
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>
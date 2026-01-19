<?php
require_once 'header.php'; 

// Подключение к базе данных
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

// Получаем параметры поиска
$query = $_GET['query'] ?? '';
$district = $_GET['district'] ?? '';
$area = $_GET['area'] ?? '';

// Строим SQL запрос
$sql = "SELECT * FROM venues WHERE 1=1";
$params = [];

if (!empty($query)) {
    $sql .= " AND (name LIKE ? OR district LIKE ? OR address LIKE ? OR adm_area LIKE ?)";
    $searchTerm = "%" . $query . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($district)) {
    $sql .= " AND district LIKE ?";
    $params[] = "%" . $district . "%";
}

if (!empty($area)) {
    $sql .= " AND adm_area LIKE ?";
    $params[] = "%" . $area . "%";
}

$sql .= " ORDER BY name";

// Выполняем запрос
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем уникальные районы и округа для фильтров
$districtsStmt = $pdo->query("SELECT DISTINCT district FROM venues WHERE district IS NOT NULL ORDER BY district");
$districts = $districtsStmt->fetchAll(PDO::FETCH_COLUMN);

$areasStmt = $pdo->query("SELECT DISTINCT adm_area FROM venues WHERE adm_area IS NOT NULL ORDER BY adm_area");
$areas = $areasStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!-- Стили только для страницы поиска -->
<style>
    /* Убираем глобальные стили, оставляем только для контента */
    .search-container { 
        max-width: 1200px; 
        margin: 20px auto; 
        background: white; 
        border-radius: 15px; 
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
    }
    
    .search-header { 
        background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%); 
        color: white; 
        padding: 40px 30px; 
        text-align: center; 
    }
    
    .search-header h1 { 
        margin-bottom: 15px; 
        font-size: 2.5em;
        font-weight: 700;
    }
    
    .subtitle {
        font-size: 1.2em;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .search-box { 
        padding: 30px; 
        background: white;
    }
    
    .search-input { 
        width: 100%; 
        padding: 15px 20px; 
        border: 2px solid #e0e0e0; 
        border-radius: 10px; 
        font-size: 16px; 
        transition: all 0.3s;
    }
    
    .search-input:focus {
        border-color: #4a6fa5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
    }
    
    .btn { 
        padding: 12px 30px; 
        background: #4a6fa5; 
        color: white; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 16px; 
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn:hover {
        background: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .btn-secondary {
        background: #7f8c8d;
    }
    
    .btn-secondary:hover {
        background: #6c7b7d;
    }
    
    .btn-details {
        padding: 10px 20px;
        background: #4a6fa5;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        margin-top: 15px;
    }
    
    .btn-details:hover {
        background: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-details::after {
        content: "→";
        font-weight: bold;
    }
    
    .results-count { 
        padding: 20px 30px; 
        background: #f8f9fa; 
        font-size: 1.2em; 
        color: #2c3e50; 
        font-weight: bold;
        border-bottom: 1px solid #e9ecef;
    }
    
    .venue-card { 
        padding: 25px 30px; 
        border-bottom: 1px solid #eee; 
        transition: background-color 0.3s;
    }
    
    .venue-card:hover {
        background-color: #f8f9fa;
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
        min-width: 120px;
        display: inline-block;
    }
    
    .filters { 
        background: #f8f9fa; 
        padding: 25px 30px; 
        margin: 0; 
        border-bottom: 1px solid #e9ecef;
    }
    
    .filter-group { 
        display: inline-block; 
        margin-right: 20px;
        margin-bottom: 15px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95em;
    }
    
    .filter-select { 
        padding: 10px 15px; 
        border: 2px solid #e0e0e0; 
        border-radius: 8px; 
        min-width: 250px;
        font-size: 15px;
        background: white;
        transition: all 0.3s;
    }
    
    .filter-select:focus {
        border-color: #4a6fa5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
    }
    
    .filters-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 20px;
    }
    
    .filter-buttons {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }
    
    .no-results {
        text-align: center; 
        padding: 60px 30px; 
        color: #7f8c8d;
    }
    
    .no-results h3 {
        font-size: 1.8em;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .no-results p {
        font-size: 1.1em;
        margin-bottom: 25px;
    }
    
    .emoji {
        font-size: 3em;
        margin-bottom: 20px;
        display: block;
    }
    
    .search-footer { 
        text-align: center; 
        padding: 25px 30px; 
        color: #7f8c8d; 
        background: #f8f9fa; 
        border-top: 1px solid #e9ecef;
    }
    
    .footer-links {
        margin-bottom: 15px;
    }
    
    .footer-links a {
        color: #4a6fa5;
        text-decoration: none;
        margin: 0 10px;
        font-weight: 500;
    }
    
    .footer-links a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .search-container {
            margin: 10px;
            border-radius: 10px;
        }
        
        .search-header {
            padding: 30px 20px;
        }
        
        .search-header h1 {
            font-size: 2em;
        }
        
        .search-box, .filters, .venue-card {
            padding: 20px;
        }
        
        .filter-group {
            display: block;
            margin-right: 0;
            margin-bottom: 15px;
            width: 100%;
        }
        
        .filter-select {
            width: 100%;
            min-width: auto;
        }
        
        .filters-form {
            flex-direction: column;
        }
        
        .filter-buttons {
            width: 100%;
            justify-content: center;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

</head>
<body>
    <!-- Шапка уже подключена через header.php и будет синей -->
    
    <main>
        <div class="search-container">
            <!-- Заголовок страницы поиска -->
            <div class="search-header">
                <h1>🔍 Поиск музыкальных площадок</h1>
                <p class="subtitle">Найдите площадки по названию, району, округу или адресу</p>
            </div>
            
            <!-- Форма поиска -->
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" name="query" class="search-input" 
                           placeholder="Введите название площадки, район, округ или адрес..." 
                           value="<?php echo htmlspecialchars($query); ?>">
                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="btn">🔍 Найти площадки</button>
                        <a href="index.php" class="btn btn-secondary">← На главную</a>
                    </div>
                </form>
            </div>
            
            <!-- Фильтры -->
            <div class="filters">
                <form method="GET" action="" class="filters-form">
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                    
                    <div class="filter-group">
                        <label>📍 Административный округ</label>
                        <select name="area" class="filter-select">
                            <option value="">Все округа</option>
                            <?php foreach ($areas as $a): ?>
                                <option value="<?php echo htmlspecialchars($a); ?>" 
                                    <?php echo ($area == $a) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($a); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>🏛️ Район</label>
                        <select name="district" class="filter-select">
                            <option value="">Все районы</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo htmlspecialchars($d); ?>"
                                    <?php echo ($district == $d) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($d); ?>
                                </option>
                            <?php endforeach; ?>
                    </select>
                    </div>
                    
                    <div class="filter-group filter-buttons">
                        <button type="submit" class="btn">Применить фильтры</button>
                        <a href="search.php" class="btn btn-secondary">Сбросить фильтры</a>
                    </div>
                </form>
            </div>
            
            <!-- Результаты -->
            <div class="results-count">
                Найдено площадок: <span style="color: #4a6fa5;"><?php echo count($results); ?></span>
            </div>
            
            <div>
                <?php if (empty($results)): ?>
                    <div class="no-results">
                        <span class="emoji">😕</span>
                        <h3>Ничего не найдено</h3>
                        <p>Попробуйте изменить критерии поиска или используйте другие ключевые слова</p>
                        <div style="margin-top: 20px;">
                            <a href="search.php" class="btn">Сбросить поиск</a>
                            <a href="index.php" class="btn btn-secondary">Вернуться на главную</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($results as $venue): ?>
                        <div class="venue-card">
                            <h3 class="venue-name"><?php echo htmlspecialchars($venue['name']); ?></h3>
                            <p class="venue-info">
                                <strong>📍 Район:</strong> <?php echo htmlspecialchars($venue['district']); ?>
                            </p>
                            <p class="venue-info">
                                <strong>🏛️ Округ:</strong> <?php echo htmlspecialchars($venue['adm_area']); ?>
                            </p>
                            <p class="venue-info">
                                <strong>📌 Адрес:</strong> <?php echo htmlspecialchars($venue['address']); ?>
                            </p>
                            <?php if ($venue['working_hours']): ?>
                                <p class="venue-info">
                                    <strong>⏰ График работы:</strong> <?php echo htmlspecialchars($venue['working_hours']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($venue['balance_holder_phone']): ?>
                                <p class="venue-info">
                                    <strong>📞 Телефон:</strong> <?php echo htmlspecialchars($venue['balance_holder_phone']); ?>
                                </p>
                            <?php endif; ?>
                            <a href="venue_detail.php?id=<?php echo $venue['id']; ?>" class="btn-details">
                                Подробнее о площадке
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
<?php require_once 'footer.php'; ?>
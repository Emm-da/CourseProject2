<?php
// test_db.php — Полный тест подключения к БД и структуры таблицы
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🔧 Тест подключения к БД</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', monospace; line-height: 1.5; padding: 20px; }
        .ok { color: #2e7d32; }
        .warn { color: #e65100; }
        .error { color: #c62828; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 6px; overflow-x: auto; }
        .card { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        h2 { color: #1a237e; }
    </style>
</head>
<body>
    <h2>🔧 Тест подключения к базе данных MySQL</h2>
    
    <div class="card">
        <h3>1. Проверка расширений PHP</h3>
        <pre>
<?php
if (!extension_loaded('pdo_mysql')) {
    echo "❌ pdo_mysql — НЕ ЗАГРУЖЕН\n";
    echo "   Решение: раскомментируйте 'extension=pdo_mysql' в php.ini\n";
} else {
    echo "✅ pdo_mysql — загружен\n";
}

if (!extension_loaded('json')) {
    echo "❌ json — НЕ ЗАГРУЖЕН\n";
} else {
    echo "✅ json — загружен\n";
}
?>
        </pre>
    </div>

    <div class="card">
        <h3>2. Подключение к базе данных</h3>
        <pre>
<?php
$host = 'localhost';
$dbname = 'music_venues_db';
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    echo "✅ Успешно подключено к MySQL\n";
    echo "   Хост: $host\n";
    echo "   База: $dbname\n";
    echo "   Пользователь: $username\n";

} catch (PDOException $e) {
    echo "❌ ОШИБКА ПОДКЛЮЧЕНИЯ:\n";
    echo "   Код: " . $e->getCode() . "\n";
    echo "   Сообщение: " . $e->getMessage() . "\n";
    exit;
}
?>
        </pre>
    </div>

    <div class="card">
        <h3>3. Список таблиц</h3>
        <pre>
<?php
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($tables)) {
    echo "⚠️ База данных пуста. Таблицы не найдены.\n";
} else {
    echo "✅ Найдены таблицы (" . count($tables) . "):\n";
    foreach ($tables as $i => $table) {
        echo "   " . ($i+1) . ". $table\n";
    }
}
?>
        </pre>
    </div>

    <?php if (in_array('venues', $tables)): ?>
    <div class="card">
        <h3>4. Структура таблицы <code>venues</code></h3>
        <pre>
<?php
$stmt = $pdo->query("DESCRIBE venues");
$columns = $stmt->fetchAll();
foreach ($columns as $col) {
    $null = ($col['Null'] === 'YES') ? 'NULL' : 'NOT NULL';
    $key = $col['Key'] ?: '';
    echo sprintf("%-25s | %-15s | %-8s | %-5s | %s\n",
        $col['Field'],
        $col['Type'],
        $null,
        $key,
        $col['Extra'] ?: ''
    );
}
?>
        </pre>
    </div>

    <div class="card">
        <h3>5. Пример данных (первые 3 записи с координатами)</h3>
        <pre>
<?php
// Используем COALESCE: сначала latitude/longitude, потом lat/lng
$stmt = $pdo->prepare("
    SELECT 
        id,
        name,
        district,
        adm_area,
        COALESCE(latitude, lat) AS final_lat,
        COALESCE(longitude, lng) AS final_lng,
        address
    FROM venues
    WHERE COALESCE(latitude, lat) IS NOT NULL 
      AND COALESCE(longitude, lng) IS NOT NULL
    ORDER BY id
    LIMIT 3
");
$stmt->execute();
$rows = $stmt->fetchAll();

if (empty($rows)) {
    echo "❗ Нет записей с координатами.\n";
    echo "   Проверьте поля: latitude, longitude, lat, lng, geo_data.\n";
} else {
    foreach ($rows as $row) {
        $lat = $row['final_lat'] ?? '—';
        $lng = $row['final_lng'] ?? '—';
        $district = htmlspecialchars($row['district'] ?? '—');
        $name = htmlspecialchars($row['name'] ?? 'Без названия');
        echo "ID: {$row['id']} | Название: $name\n";
        echo "   Район: $district | Координаты: $lat, $lng\n";
        echo "   Адрес: " . htmlspecialchars($row['address'] ?? '—') . "\n";
        echo "   ---\n";
    }
}
?>
        </pre>
    </div>

    <div class="card">
        <h3>6. Статистика по координатам</h3>
        <pre>
<?php
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        COUNT(latitude) AS with_latitude,
        COUNT(longitude) AS with_longitude,
        COUNT(lat) AS with_lat,
        COUNT(lng) AS with_lng,
        COUNT(geo_data) AS with_geo_data
    FROM venues
")->fetch();

echo "Всего записей:               {$stats['total']}\n";
echo "С latitude:                  {$stats['with_latitude']}\n";
echo "С longitude:                 {$stats['with_longitude']}\n";
echo "С lat:                       {$stats['with_lat']}\n";
echo "С lng:                       {$stats['with_lng']}\n";
echo "С geo_data (JSON):           {$stats['with_geo_data']}\n";

// Вывод рекомендации
if ($stats['with_latitude'] > 0 && $stats['with_longitude'] > 0) {
    echo "\n✅ Рекомендация: используйте поля `latitude` и `longitude` в Java-коде.\n";
} elseif ($stats['with_lat'] > 0 && $stats['with_lng'] > 0) {
    echo "\n⚠️ Рекомендация: `latitude`/`longitude` пусты — используйте `lat`/`lng`.\n";
} elseif ($stats['with_geo_data'] > 0) {
    echo "\n💡 Рекомендация: координаты, вероятно, в JSON-поле `geo_data`.\n";
} else {
    echo "\n❗ Нужно импортировать координаты из датасета.\n";
}
?>
        </pre>
    </div>

    <div class="card">
        <h3>7. Проверка JSON-поля <code>geo_data</code> (если есть)</h3>
        <pre>
<?php
$stmt = $pdo->query("SELECT geo_data FROM venues WHERE geo_data IS NOT NULL LIMIT 1");
$geo = $stmt->fetchColumn();

if ($geo) {
    echo "Пример geo_data:\n";
    echo wordwrap(htmlspecialchars($geo), 100, "\n   ", true) . "\n";
    
    // Попробуем распарсить
    $data = json_decode($geo, true);
    if ($data && isset($data['coordinates']) && is_array($data['coordinates'])) {
        $coords = $data['coordinates'];
        if (count($coords) >= 2) {
            echo "\n✅ Распарсено: lng={$coords[0]}, lat={$coords[1]}\n";
        }
    }
} else {
    echo "Поле geo_data пусто или отсутствует в первой записи.\n";
}
?>
        </pre>
    </div>
    <?php endif; ?>

    <hr>
    <p><a href="index.php">← Вернуться на главную</a></p>
</body>
</html>
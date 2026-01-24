<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit();
}

$host = 'localhost';
$dbname = 'music_venues_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        venue_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_favorite (user_id, venue_id)
    )
");

$venue_id = isset($_POST['venue_id']) ? intval($_POST['venue_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($venue_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID площадки']);
    exit();
}

try {
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, venue_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $venue_id]);
        
        echo json_encode([
            'success' => true, 
            'action' => 'added',
            'message' => 'Добавлено в избранное'
        ]);
        
    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND venue_id = ?");
        $stmt->execute([$_SESSION['user_id'], $venue_id]);
        
        echo json_encode([
            'success' => true, 
            'action' => 'removed',
            'message' => 'Удалено из избранного'
        ]);
        
    } elseif ($action === 'check') {
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND venue_id = ?");
        $stmt->execute([$_SESSION['user_id'], $venue_id]);
        
        echo json_encode([
            'success' => true,
            'is_favorite' => $stmt->fetch() ? true : false
        ]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}
?>
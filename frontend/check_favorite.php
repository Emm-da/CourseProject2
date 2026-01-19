<?php
// check_favorite.php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['is_favorite' => false]);
    exit();
}

$venue_id = $_GET['venue_id'] ?? 0;

if ($venue_id) {
    $stmt = $pdo->prepare("
        SELECT id FROM favorites 
        WHERE user_id = ? AND venue_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $venue_id]);
    
    echo json_encode(['is_favorite' => (bool)$stmt->fetch()]);
} else {
    echo json_encode(['is_favorite' => false]);
}
?>
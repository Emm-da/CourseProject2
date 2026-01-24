<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
        exit();
    }
    
    $venue_id = isset($_POST['venue_id']) ? intval($_POST['venue_id']) : 0;
    $ajax_action = $_POST['ajax_action'];
    
    if ($venue_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID площадки']);
        exit();
    }
    
    try {
        if ($ajax_action === 'toggle') {
            $check_stmt = $pdo->prepare("
                SELECT id FROM favorites 
                WHERE user_id = ? AND venue_id = ?
            ");
            $check_stmt->execute([$_SESSION['user_id'], $venue_id]);
            
            if ($check_stmt->fetch()) {
                $delete_stmt = $pdo->prepare("
                    DELETE FROM favorites 
                    WHERE user_id = ? AND venue_id = ?
                ");
                $delete_stmt->execute([$_SESSION['user_id'], $venue_id]);
                
                echo json_encode([
                    'success' => true, 
                    'action' => 'removed',
                    'message' => 'Удалено из избранного'
                ]);
            } else {
                $insert_stmt = $pdo->prepare("
                    INSERT INTO favorites (user_id, venue_id, created_at) 
                    VALUES (?, ?, NOW())
                ");
                $insert_stmt->execute([$_SESSION['user_id'], $venue_id]);
                
                echo json_encode([
                    'success' => true, 
                    'action' => 'added',
                    'message' => 'Добавлено в избранное'
                ]);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit();
}
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT v.* 
    FROM venues v
    JOIN favorites f ON v.id = f.venue_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();

if (isset($_GET['remove'])) {
    $venue_id = $_GET['remove'];
    
    $delete_stmt = $pdo->prepare("
        DELETE FROM favorites 
        WHERE user_id = ? AND venue_id = ?
    ");
    $delete_stmt->execute([$_SESSION['user_id'], $venue_id]);
    
    header('Location: favorites.php?removed=1');
    exit();
}

if (isset($_POST['add_to_favorites'])) {
    $venue_id = $_POST['venue_id'] ?? 0;
    
    if ($venue_id) {
        $check_stmt = $pdo->prepare("
            SELECT id FROM favorites 
            WHERE user_id = ? AND venue_id = ?
        ");
        $check_stmt->execute([$_SESSION['user_id'], $venue_id]);
        
        if (!$check_stmt->fetch()) {
            $insert_stmt = $pdo->prepare("
                INSERT INTO favorites (user_id, venue_id, created_at) 
                VALUES (?, ?, NOW())
            ");
            $insert_stmt->execute([$_SESSION['user_id'], $venue_id]);
        }
        
        echo json_encode(['success' => true]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранные площадки</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 50px 0;
            text-align: center;
            margin-bottom: 40px;
            border-radius: 0 0 20px 20px;
        }
        
        .page-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .page-header p {
            opacity: 0.9;
            font-size: 1.2em;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .favorites-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .favorites-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .favorites-header h2 {
            color: #2c3e50;
            font-size: 1.8em;
        }
        
        .favorites-count {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .favorite-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 2px solid #f8f9fa;
        }
        
        .favorite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            border-color: #e74c3c;
        }
        
        .favorite-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .favorite-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .favorite-card:hover .favorite-image img {
            transform: scale(1.05);
        }
        
        .favorite-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .favorite-content {
            padding: 25px;
        }
        
        .favorite-content h3 {
            color: #2c3e50;
            font-size: 1.3em;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .favorite-info {
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .info-item i {
            color: #e74c3c;
            font-size: 1.1em;
            width: 20px;
            text-align: center;
            margin-top: 2px;
        }
        
        .info-item span {
            color: #555;
            font-size: 0.95em;
            line-height: 1.4;
        }
        
        .favorite-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
        }
        
        .btn-primary {
            background: #4a6fa5;
            color: white;
            flex-grow: 1;
            justify-content: center;
        }
        
        .btn-primary:hover {
            background: #3a5a80;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .no-favorites {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            grid-column: 1 / -1;
        }
        
        .no-favorites h3 {
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .no-favorites p {
            margin-bottom: 25px;
            font-size: 1.1em;
        }
        
        .empty-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .discover-button {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 30px;
            background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .discover-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
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
        
        .quick-actions {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .favorites-container {
                padding: 25px;
            }
            
            .favorites-grid {
                grid-template-columns: 1fr;
            }
            
            .favorites-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .favorite-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'header.php'; ?>
    
    <main>
        <div class="page-header">
            <div class="container">
                <h1>❤️ Избранные площадки</h1>
                <p>Ваша личная коллекция любимых музыкальных площадок Москвы</p>
            </div>
        </div>
        
        <div class="container">
            <div class="quick-actions">
                <a href="search.php" class="discover-button">
                    <i class="fas fa-search"></i> Найти новые площадки
                </a>
                <a href="recommendations.php" class="discover-button" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                    <i class="fas fa-star"></i> Получить рекомендации
                </a>
            </div>
            
            <div class="favorites-container">
                <div class="favorites-header">
                    <h2>Мои избранные</h2>
                    <div class="favorites-count">
                        <?php echo count($favorites); ?> площадок
                    </div>
                </div>
                
                <?php if (empty($favorites)): ?>
                    <div class="no-favorites">
                        <div class="empty-icon">❤️</div>
                        <h3>У вас пока нет избранных площадок</h3>
                        <p>Нажимайте на сердечко ❤️ на страницах площадок, чтобы добавить их в избранное</p>
                        <p>Здесь вы сможете быстро находить площадки, которые понравились вам больше всего</p>
                        
                        <a href="search.php" class="discover-button">
                            <i class="fas fa-search"></i> Начать поиск площадок
                        </a>
                    </div>
                <?php else: ?>
                    <div class="favorites-grid">
                        <?php foreach ($favorites as $venue): ?>
                            <?php
                            $photo_stmt = $pdo->prepare("
                                SELECT photo_url FROM venue_photos 
                                WHERE venue_id = ? AND is_main = 1 
                                LIMIT 1
                            ");
                            $photo_stmt->execute([$venue['id']]);
                            $photo = $photo_stmt->fetch();
                            $photo_url = $photo['photo_url'] ?? 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80';
                            ?>
                            
                            <div class="favorite-card">
                                <div class="favorite-image">
                                    <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>">
                                    <div class="favorite-badge">
                                        <i class="fas fa-heart"></i> В избранном
                                    </div>
                                </div>
                                
                                <div class="favorite-content">
                                    <h3><?php echo htmlspecialchars($venue['name']); ?></h3>
                                    
                                    <div class="favorite-info">
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($venue['address']); ?></span>
                                        </div>
                                        
                                        <div class="info-item">
                                            <i class="fas fa-city"></i>
                                            <span><?php echo htmlspecialchars($venue['district']); ?>, <?php echo htmlspecialchars($venue['adm_area']); ?></span>
                                        </div>
                                        
                                        <?php if ($venue['working_hours']): ?>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span><?php echo htmlspecialchars($venue['working_hours']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($venue['balance_holder_phone']): ?>
                                            <div class="info-item">
                                                <i class="fas fa-phone"></i>
                                                <span><?php echo htmlspecialchars($venue['balance_holder_phone']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="favorite-actions">
                                        <a href="venue_detail.php?id=<?php echo $venue['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i> Подробнее
                                        </a>
                                        <a href="favorites.php?remove=<?php echo $venue['id']; ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Удалить площадку из избранного?')">
                                            <i class="fas fa-trash"></i> Удалить
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php require_once 'footer.php'; ?>
    
    <script>
        <?php if (isset($_GET['removed'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.innerHTML = `
                    <span style="font-size: 1.3em;">✅</span>
                    <span>Площадка удалена из избранного</span>
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.animation = 'slideInRight 0.3s ease-out reverse';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            });
        <?php endif; ?>
        
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.favorite-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
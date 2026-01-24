<?php
$page_title = 'Детали площадки - Музыкальные площадки Москвы';
require_once 'header.php';

$host = 'localhost';
$dbname = 'music_venues_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$venue_id = $_GET['id'] ?? 1;

$stmt = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->execute([$venue_id]);
$venue = $stmt->fetch();

$photos_stmt = $pdo->prepare("SELECT * FROM venue_photos WHERE venue_id = ? ORDER BY is_main DESC");
$photos_stmt->execute([$venue_id]);
$photos = $photos_stmt->fetchAll();

$features_stmt = $pdo->prepare("SELECT feature_name, feature_value FROM venue_features WHERE venue_id = ?");
$features_stmt->execute([$venue_id]);
$features = $features_stmt->fetchAll();

if(!$venue) {
    die("Площадка не найдена");
}
?>
<style>
    main {
        padding: 40px 0;
        min-height: 70vh;
    }
    
    .venue-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 992px) {
        .venue-detail {
            grid-template-columns: 1fr;
        }
    }
    
    .venue-images {
        position: relative;
    }
    
    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .thumbnail-images {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        transition: transform 0.3s;
        border: 2px solid transparent;
    }
    
    .thumbnail:hover,
    .thumbnail.active {
        transform: scale(1.1);
        border-color: #4a6fa5;
    }
    
    .venue-info {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .venue-info h1 {
        color: #2c3e50;
        font-size: 2.2em;
        margin-bottom: 5px;
    }
    
    .venue-category {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4a6fa5;
        font-weight: 500;
        margin-bottom: 10px;
    }
    
    .venue-category i {
        font-size: 1.2em;
    }
    
    .venue-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
    }
    
    .meta-item i {
        color: #4a6fa5;
    }
    
    .status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 500;
        background: #e8f4ff;
        color: #4a6fa5;
        margin-bottom: 20px;
    }
    
    .status i {
        font-size: 1.1em;
    }
    
    .description {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    
    .description h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.3em;
    }
    
    .description p {
        line-height: 1.7;
        color: #555;
    }
    
    .features {
        margin-bottom: 25px;
    }
    
    .features h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.3em;
    }
    
    .features-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .features-table tr {
        border-bottom: 1px solid #eee;
    }
    
    .features-table tr:last-child {
        border-bottom: none;
    }
    
    .features-table td {
        padding: 12px 15px;
    }
    
    .features-table td:first-child {
        font-weight: 600;
        color: #2c3e50;
        width: 40%;
        background: #f8f9fa;
    }
    
    .venue-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-primary {
        background: #4a6fa5;
        color: white;
    }
    
    .btn-primary:hover {
        background: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .btn-secondary {
        background: #f8f9fa;
        color: #666;
        border: 2px solid #e0e0e0;
    }
    
    .btn-secondary:hover {
        background: #e9ecef;
    }
    
    #favoriteBtn.active {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }
    
    #favoriteBtn.active:hover {
        background: #c0392b;
        border-color: #c0392b;
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
    
    .notification.error {
        background: #e74c3c;
    }
    
    .notification.info {
        background: #3498db;
    }
    
    .notification.hidden {
        animation: slideOutRight 0.3s ease-out forwards;
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
    
    .contact-info {
        background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        margin-top: 30px;
    }
    
    .contact-info h3 {
        margin-bottom: 20px;
        font-size: 1.3em;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .contact-item i {
        font-size: 1.3em;
        background: rgba(255,255,255,0.2);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .contact-text {
        flex-grow: 1;
    }
    
    .contact-text strong {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9em;
        opacity: 0.9;
    }
    
    .contact-text a {
        color: white;
        text-decoration: underline;
        transition: opacity 0.3s;
    }
    
    .contact-text a:hover {
        opacity: 0.8;
    }
    
    @media (max-width: 768px) {
        .venue-detail {
            padding: 20px;
        }
        
        .venue-info h1 {
            font-size: 1.8em;
        }
        
        .venue-meta {
            flex-direction: column;
            gap: 10px;
        }
        
        .venue-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    .text-center { text-align: center; }
    .mt-20 { margin-top: 20px; }
    .mb-20 { margin-bottom: 20px; }
    .mt-30 { margin-top: 30px; }
    .mb-30 { margin-bottom: 30px; }
</style>

<main class="container">
    <div class="venue-detail">
        <div class="venue-images">
            <?php if(!empty($photos)): ?>
                <img src="<?php echo htmlspecialchars($photos[0]['photo_url']); ?>" 
                     alt="<?php echo htmlspecialchars($venue['name']); ?>" 
                     class="main-image" id="mainImage">
                
                <?php if(count($photos) > 1): ?>
                    <div class="thumbnail-images">
                        <?php foreach($photos as $index => $photo): ?>
                            <img src="<?php echo htmlspecialchars($photo['photo_url']); ?>" 
                                 alt="Фото <?php echo $index + 1; ?>"
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage('<?php echo htmlspecialchars($photo['photo_url']); ?>', this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                     alt="Заглушка"
                     class="main-image">
            <?php endif; ?>
        </div>
        
        <div class="venue-info">
            <h1><?php echo htmlspecialchars($venue['name']); ?></h1>
            
            <div class="venue-category">
                <i class="fas fa-guitar"></i>
                <span><?php echo htmlspecialchars($venue['type'] ?? 'Музыкальная площадка'); ?></span>
            </div>
            
            <div class="venue-meta">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($venue['district']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-city"></i>
                    <span><?php echo htmlspecialchars($venue['adm_area']); ?></span>
                </div>
            </div>
            
            <div class="status">
                <i class="fas fa-check-circle"></i>
                <span>Доступна для бронирования</span>
            </div>
            
            <div class="description">
                <h3>Описание площадки</h3>
                <p><?php echo nl2br(htmlspecialchars($venue['description'] ?? 'Подробное описание площадки...')); ?></p>
            </div>
            
            <div class="features">
                <h3>Характеристики</h3>
                <table class="features-table">
                    <tr>
                        <td>Адрес:</td>
                        <td><?php echo htmlspecialchars($venue['address']); ?></td>
                    </tr>
                    <tr>
                        <td>Вместимость:</td>
                        <td><?php echo htmlspecialchars($venue['capacity'] ?? 'Не указана'); ?> человек</td>
                    </tr>
                    <tr>
                        <td>Размер сцены:</td>
                        <td><?php echo htmlspecialchars($venue['stage_size'] ?? 'Не указан'); ?></td>
                    </tr>
                    <tr>
                        <td>График работы:</td>
                        <td><?php echo htmlspecialchars($venue['working_hours'] ?? 'Не указан'); ?></td>
                    </tr>
                    <tr>
                        <td>Стоимость аренды:</td>
                        <td><?php echo htmlspecialchars($venue['rent_price'] ?? 'По запросу'); ?></td>
                    </tr>
                    <?php foreach($features as $feature): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feature['feature_name']); ?>:</td>
                        <td><?php echo htmlspecialchars($feature['feature_value']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="venue-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button id="favoriteBtn" class="btn btn-secondary" onclick="toggleFavorite(<?php echo $venue['id']; ?>)">
                        <i class="far fa-heart" id="favoriteIcon"></i>
                        <span id="favoriteText">В избранное</span>
                    </button>
                    
                    <a href="booking.php?id=<?php echo $venue['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Забронировать
                    </a>
                    
                    <button class="btn btn-secondary" onclick="shareVenue()">
                        <i class="fas fa-share-alt"></i> Поделиться
                    </button>
                <?php else: ?>
                    <a href="login.php?redirect=booking&venue_id=<?php echo $venue['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Забронировать
                    </a>
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                    <a href="register.php?redirect=booking&venue_id=<?php echo $venue['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Регистрация
                    </a>
                <?php endif; ?>
                
                <a href="search.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад к поиску
                </a>
            </div>
        </div>
    </div>
    
    <div class="contact-info mt-30">
        <h3>Контактная информация</h3>
        <div class="contact-grid">
            <?php if($venue['balance_holder_phone']): ?>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div class="contact-text">
                    <strong>Телефон для бронирования</strong>
                    <div><?php echo htmlspecialchars($venue['balance_holder_phone']); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($venue['balance_holder_email'])): ?>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div class="contact-text">
                    <strong>Email</strong>
                    <div><?php echo htmlspecialchars($venue['balance_holder_email']); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($venue['balance_holder_website'])): ?>
            <div class="contact-item">
                <i class="fas fa-globe"></i>
                <div class="contact-text">
                    <strong>Веб-сайт</strong>
                    <div>
                        <?php 
                        $url = $venue['balance_holder_website'];
                        if(!empty($url)) {
                            $display_url = htmlspecialchars($url);
                            $link_url = $url;
                            if(!preg_match('/^https?:\/\//', $url)) {
                                $link_url = 'http://' . $url;
                            }
                            ?>
                            <a href="<?php echo htmlspecialchars($link_url); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo $display_url; ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="contact-item">
                <i class="fas fa-map-marked-alt"></i>
                <div class="contact-text">
                    <strong>Расположение</strong>
                    <div>
                        <?php echo htmlspecialchars($venue['district']); ?>, 
                        <?php echo htmlspecialchars($venue['adm_area']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>

<script>
    function changeMainImage(src, element) {
        document.getElementById('mainImage').src = src;
        
        document.querySelectorAll('.thumbnail').forEach(img => {
            img.classList.remove('active');
        });
        
        element.classList.add('active');
    }
    
    function shareVenue() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($venue["name"]); ?>',
                text: 'Посмотрите эту музыкальную площадку в Москве',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href)
                .then(() => showNotification('Ссылка скопирована в буфер обмена!', 'success'));
        }
    }
    
    const mainImage = document.getElementById('mainImage');
    let isZoomed = false;
    
    mainImage.addEventListener('click', function() {
        if (isZoomed) {
            this.style.transform = 'scale(1)';
            this.style.cursor = 'zoom-in';
        } else {
            this.style.transform = 'scale(1.5)';
            this.style.cursor = 'zoom-out';
        }
        isZoomed = !isZoomed;
    });

    function checkFavoriteStatus(venueId) {
        fetch('ajax_favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=check&venue_id=' + venueId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_favorite) {
                updateFavoriteButton(true);
            }
        })
        .catch(error => console.error('Ошибка проверки:', error));
    }

    function toggleFavorite(venueId) {
        const favoriteBtn = document.getElementById('favoriteBtn');
        const isCurrentlyFavorite = favoriteBtn.classList.contains('active');
        
        fetch('ajax_favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=' + (isCurrentlyFavorite ? 'remove' : 'add') + '&venue_id=' + venueId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFavoriteButton(!isCurrentlyFavorite);
                showNotification(data.message, isCurrentlyFavorite ? 'info' : 'success');
            } else {
                showNotification(data.message || 'Ошибка', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showNotification('Ошибка соединения', 'error');
        });
    }

    function updateFavoriteButton(isFavorite) {
        const favoriteBtn = document.getElementById('favoriteBtn');
        const favoriteIcon = document.getElementById('favoriteIcon');
        const favoriteText = document.getElementById('favoriteText');
        
        if (isFavorite) {
            favoriteBtn.classList.add('active');
            favoriteIcon.classList.remove('far');
            favoriteIcon.classList.add('fas');
            favoriteText.textContent = 'В избранном';
        } else {
            favoriteBtn.classList.remove('active');
            favoriteIcon.classList.remove('fas');
            favoriteIcon.classList.add('far');
            favoriteText.textContent = 'В избранное';
        }
    }
    
    function showNotification(message, type = 'success') {
        const oldNotification = document.querySelector('.notification:not(.hidden)');
        if (oldNotification) {
            oldNotification.classList.add('hidden');
            setTimeout(() => {
                if (oldNotification.parentNode) {
                    oldNotification.parentNode.removeChild(oldNotification);
                }
            }, 300);
        }
        
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.innerHTML = `
            <span style="font-size: 1.3em;">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('hidden');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['user_id']) && isset($venue['id'])): ?>
            checkFavoriteStatus(<?php echo $venue['id']; ?>);
        <?php endif; ?>
    });
</script>
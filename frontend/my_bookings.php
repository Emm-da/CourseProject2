<?php
session_start();

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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT b.*, v.name as venue_name, v.address, v.district 
    FROM bookings b
    JOIN venues v ON b.venue_id = v.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'] ?? 0;
    
    if ($booking_id) {
        $cancel_stmt = $pdo->prepare("
            UPDATE bookings SET status = 'cancelled' 
            WHERE id = ? AND user_id = ?
        ");
        $cancel_stmt->execute([$booking_id, $_SESSION['user_id']]);
        
        header('Location: my_bookings.php?cancelled=1');
        exit();
    }
}

$page_title = 'Мои бронирования';
require_once 'header.php';
?>

<style>
    .bookings-container { 
        max-width: 1200px; 
        margin: 20px auto; 
        background: white; 
        border-radius: 15px; 
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
    }
    
    .bookings-header { 
        background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%); 
        color: white; 
        padding: 40px 30px; 
        text-align: center; 
    }
    
    .bookings-header h1 { 
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
    
    .controls { 
        padding: 30px; 
        background: white;
        border-bottom: 1px solid #e9ecef;
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
    
    .btn-danger {
        background: #e74c3c;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
    
    .results-count { 
        padding: 20px 30px; 
        background: #f8f9fa; 
        font-size: 1.2em; 
        color: #2c3e50; 
        font-weight: bold;
        border-bottom: 1px solid #e9ecef;
    }
    
    .booking-card { 
        padding: 25px 30px; 
        border-bottom: 1px solid #eee; 
        transition: background-color 0.3s;
    }
    
    .booking-card:hover {
        background-color: #f8f9fa;
    }
    
    .booking-name { 
        color: #2c3e50; 
        font-size: 1.4em; 
        margin-bottom: 15px; 
        font-weight: 600;
    }
    
    .booking-info { 
        color: #555; 
        margin-bottom: 8px; 
        font-size: 0.95em;
    }
    
    .booking-info strong {
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
        min-width: 200px;
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
        display: flex;
        gap: 10px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 10px 25px;
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .filter-btn.active {
        background: #4a6fa5;
        color: white;
        border-color: #4a6fa5;
    }
    
    .filter-btn:hover:not(.active) {
        border-color: #4a6fa5;
        color: #4a6fa5;
    }
    
    .booking-status {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        text-transform: uppercase;
        margin-left: 15px;
    }
    
    .status-pending {
        background: #fef9e7;
        color: #b7950b;
    }
    
    .status-confirmed {
        background: #eafaf1;
        color: #27ae60;
    }
    
    .status-cancelled {
        background: #fdedec;
        color: #c0392b;
    }
    
    .status-completed {
        background: #e8f4fc;
        color: #2980b9;
    }
    
    .booking-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .no-bookings {
        text-align: center; 
        padding: 60px 30px; 
        color: #7f8c8d;
    }
    
    .no-bookings h3 {
        font-size: 1.8em;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .no-bookings p {
        font-size: 1.1em;
        margin-bottom: 25px;
    }
    
    .emoji {
        font-size: 3em;
        margin-bottom: 20px;
        display: block;
    }
    
    .bookings-footer { 
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
    
    @media (max-width: 768px) {
        .bookings-container {
            margin: 10px;
            border-radius: 10px;
        }
        
        .bookings-header {
            padding: 30px 20px;
        }
        
        .bookings-header h1 {
            font-size: 2em;
        }
        
        .controls, .filters, .booking-card {
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
        
        .booking-actions {
            flex-direction: column;
        }
        
        .booking-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<main>
    <div class="bookings-container">
        <div class="bookings-header">
            <h1>Мои бронирования</h1>
            <p class="subtitle">Здесь вы можете просмотреть все ваши заявки на бронирование музыкальных площадок</p>
        </div>
        
        <div class="controls">
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="search.php" class="btn">Найти новые площадки</a>
                <a href="index.php" class="btn btn-secondary">На главную</a>
            </div>
        </div>
        
        <div class="filters">
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="filterBookings('all')">Все</button>
                <button class="filter-btn" onclick="filterBookings('pending')">Ожидание</button>
                <button class="filter-btn" onclick="filterBookings('confirmed')">Подтвержденные</button>
                <button class="filter-btn" onclick="filterBookings('cancelled')">Отмененные</button>
                <button class="filter-btn" onclick="filterBookings('completed')">Завершенные</button>
            </div>
        </div>
        
        <div class="results-count">
            Найдено бронирований: <span style="color: #4a6fa5;"><?php echo count($bookings); ?></span>
        </div>
        
        <div id="bookingsList">
            <?php if (empty($bookings)): ?>
                <div class="no-bookings">
                    <h3>У вас пока нет бронирований</h3>
                    <p>Найдите подходящую музыкальную площадку и забронируйте её для своего мероприятия</p>
                    <div style="margin-top: 20px;">
                        <a href="search.php" class="btn">Найти площадку</a>
                        <a href="index.php" class="btn btn-secondary">На главную</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php 
                    $status_class = '';
                    $status_text = '';
                    
                    switch ($booking['status']) {
                        case 'pending':
                            $status_class = 'status-pending';
                            $status_text = 'Ожидание';
                            break;
                        case 'confirmed':
                            $status_class = 'status-confirmed';
                            $status_text = 'Подтверждено';
                            break;
                        case 'cancelled':
                            $status_class = 'status-cancelled';
                            $status_text = 'Отменено';
                            break;
                        case 'completed':
                            $status_class = 'status-completed';
                            $status_text = 'Завершено';
                            break;
                    }
                    
                    $booking_date = date('d.m.Y', strtotime($booking['booking_date']));
                    $start_time = date('H:i', strtotime($booking['start_time']));
                    $end_time = date('H:i', strtotime($booking['end_time']));
                    ?>
                    
                    <div class="booking-card" data-status="<?php echo $booking['status']; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <h3 class="booking-name"><?php echo htmlspecialchars($booking['venue_name']); ?></h3>
                            <span class="booking-status <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                        
                        <p class="booking-info">
                            <strong>Адрес:</strong> <?php echo htmlspecialchars($booking['address']); ?>
                        </p>
                        <p class="booking-info">
                            <strong>Дата:</strong> <?php echo $booking_date; ?>
                        </p>
                        <p class="booking-info">
                            <strong>Время:</strong> <?php echo $start_time; ?> - <?php echo $end_time; ?>
                        </p>
                        <p class="booking-info">
                            <strong>Цель:</strong> <?php echo htmlspecialchars($booking['purpose']); ?>
                        </p>
                        <p class="booking-info">
                            <strong>Участников:</strong> <?php echo $booking['attendees_count']; ?> человек
                        </p>
                        <p class="booking-info">
                            <strong>Контактный телефон:</strong> <?php echo htmlspecialchars($booking['contact_phone']); ?>
                        </p>
                        <p class="booking-info">
                            <strong>ID бронирования:</strong> #<?php echo $booking['id']; ?>
                        </p>
                        
                        <?php if ($booking['notes']): ?>
                            <div style="margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 3px solid #4a6fa5;">
                                <strong>Дополнительные пожелания:</strong>
                                <p style="margin-top: 5px; color: #555;"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="booking-actions">
                            <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger"
                                            onclick="return confirm('Вы уверены, что хотите отменить это бронирование?')">
                                        Отменить бронирование
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="venue_detail.php?id=<?php echo $booking['venue_id']; ?>" class="btn">
                                Посмотреть площадку
                            </a>
                            
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="booking.php?id=<?php echo $booking['venue_id']; ?>" class="btn btn-secondary">
                                    Изменить бронирование
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>

<script>
    function filterBookings(status) {
        const bookings = document.querySelectorAll('.booking-card');
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.textContent.toLowerCase().includes(status)) {
                btn.classList.add('active');
            }
        });
        
        bookings.forEach(booking => {
            if (status === 'all' || booking.dataset.status === status) {
                booking.style.display = 'block';
                setTimeout(() => {
                    booking.style.opacity = '1';
                    booking.style.transform = 'translateY(0)';
                }, 10);
            } else {
                booking.style.display = 'none';
            }
        });
    }
    
    <?php if (isset($_GET['cancelled'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <span>Бронирование успешно отменено</span>
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
        const bookings = document.querySelectorAll('.booking-card');
        bookings.forEach((booking, index) => {
            booking.style.opacity = '0';
            booking.style.transform = 'translateY(20px)';
            booking.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                booking.style.opacity = '1';
                booking.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
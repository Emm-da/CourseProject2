<?php
session_start();
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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=booking&venue_id=' . ($_GET['id'] ?? 0));
    exit();
}

$venue_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->execute([$venue_id]);
$venue = $stmt->fetch();

if (!$venue) {
    header('Location: search.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_date = $_POST['booking_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $attendees = $_POST['attendees'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($booking_date)) $errors[] = 'Выберите дату';
    if (empty($start_time)) $errors[] = 'Выберите время начала';
    if (empty($end_time)) $errors[] = 'Выберите время окончания';
    if (empty($purpose)) $errors[] = 'Укажите цель мероприятия';
    if (empty($attendees)) $errors[] = 'Укажите количество участников';
    if (empty($contact_phone)) $errors[] = 'Укажите контактный телефон';
    
    if (strtotime($end_time) <= strtotime($start_time)) {
        $errors[] = 'Время окончания должно быть позже времени начала';
    }
    
    if (empty($errors)) {
        try {
            $check_stmt = $pdo->prepare("
                SELECT COUNT(*) FROM bookings 
                WHERE venue_id = ? 
                AND booking_date = ? 
                AND (
                    (start_time <= ? AND end_time >= ?) OR
                    (start_time <= ? AND end_time >= ?) OR
                    (start_time >= ? AND end_time <= ?)
                )
                AND status IN ('pending', 'confirmed')
            ");
            $check_stmt->execute([
                $venue_id,
                $booking_date,
                $start_time, $start_time,
                $end_time, $end_time,
                $start_time, $end_time
            ]);
            $conflict_count = $check_stmt->fetchColumn();
            
            if ($conflict_count > 0) {
                $errors[] = 'На это время уже есть бронирование. Выберите другое время.';
            } else {
                $insert_stmt = $pdo->prepare("
                    INSERT INTO bookings (user_id, venue_id, booking_date, start_time, end_time, 
                                         purpose, attendees_count, contact_phone, contact_email, notes, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                
                $insert_stmt->execute([
                    $_SESSION['user_id'],
                    $venue_id,
                    $booking_date,
                    $start_time,
                    $end_time,
                    $purpose,
                    $attendees,
                    $contact_phone,
                    $_SESSION['email'],
                    $notes
                ]);
                
                $success = true;
                $booking_id = $pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при создании бронирования: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование - <?php echo htmlspecialchars($venue['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .booking-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .booking-header {
            background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
            color: white;
            padding: 40px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        
        .booking-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .booking-header p {
            opacity: 0.9;
            font-size: 1.2em;
        }
        
        .booking-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .booking-content {
                grid-template-columns: 1fr;
            }
        }
        
        .venue-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #4a6fa5;
        }
        
        .venue-info h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .booking-form {
            background: white;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 1em;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #4a6fa5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
            background: white;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .btn {
            padding: 15px 30px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            text-decoration: none;
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
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .calendar-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px solid #e0e0e0;
        }
        
        .booking-success {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .success-icon {
            font-size: 4em;
            color: #2ecc71;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php 
    $page_title = 'Бронирование - ' . htmlspecialchars($venue['name']);
    require_once 'header.php'; 
    ?>
    
    <main>
        <div class="booking-container">
            <?php if ($success): ?>
                <div class="booking-success">
                    <div class="success-icon">✅</div>
                    <h1>Бронирование успешно создано!</h1>
                    <p>Ваше бронирование площадки <strong><?php echo htmlspecialchars($venue['name']); ?></strong> подтверждено.</p>
                    <p>ID бронирования: <strong>#<?php echo $booking_id; ?></strong></p>
                    <p>Мы свяжемся с вами в ближайшее время для подтверждения деталей.</p>
                    
                    <div class="form-actions" style="justify-content: center; margin-top: 30px;">
                        <a href="my_bookings.php" class="btn">
                            <i class="fas fa-calendar-alt"></i> Мои бронирования
                        </a>
                        <a href="venue_detail.php?id=<?php echo $venue_id; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Вернуться к площадке
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="booking-header">
                    <h1>Бронирование площадки</h1>
                    <p><?php echo htmlspecialchars($venue['name']); ?></p>
                </div>
                
                <div class="booking-content">
                    <div class="venue-info">
                        <h3>Информация о площадке</h3>
                        <p><strong>📍 Адрес:</strong> <?php echo htmlspecialchars($venue['address']); ?></p>
                        <p><strong>🏛️ Район:</strong> <?php echo htmlspecialchars($venue['district']); ?></p>
                        <?php if ($venue['balance_holder_phone']): ?>
                            <p><strong>📞 Телефон:</strong> <?php echo htmlspecialchars($venue['balance_holder_phone']); ?></p>
                        <?php endif; ?>
                        <?php if ($venue['working_hours']): ?>
                            <p><strong>⏰ График:</strong> <?php echo htmlspecialchars($venue['working_hours']); ?></p>
                        <?php endif; ?>
                        <?php if ($venue['balance_holder']): ?>
                            <p><strong>💼 Владелец:</strong> <?php echo htmlspecialchars($venue['balance_holder']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-form">
                        <?php if (!empty($errors)): ?>
                            <div class="error-message">
                                <strong>Ошибки:</strong>
                                <ul style="margin-top: 10px; margin-left: 20px;">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="booking_date">📅 Дата бронирования</label>
                                <input type="date" id="booking_date" name="booking_date" 
                                       class="form-control" required 
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($_POST['booking_date'] ?? date('Y-m-d')); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="start_time">⏰ Время начала</label>
                                <input type="time" id="start_time" name="start_time" 
                                       class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['start_time'] ?? '18:00'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="end_time">⏰ Время окончания</label>
                                <input type="time" id="end_time" name="end_time" 
                                       class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['end_time'] ?? '22:00'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="purpose">🎭 Цель мероприятия</label>
                                <select id="purpose" name="purpose" class="form-control" required>
                                    <option value="">Выберите цель...</option>
                                    <option value="concert" <?php echo ($_POST['purpose'] ?? '') == 'concert' ? 'selected' : ''; ?>>Концерт</option>
                                    <option value="rehearsal" <?php echo ($_POST['purpose'] ?? '') == 'rehearsal' ? 'selected' : ''; ?>>Репетиция</option>
                                    <option value="festival" <?php echo ($_POST['purpose'] ?? '') == 'festival' ? 'selected' : ''; ?>>Фестиваль</option>
                                    <option value="corporate" <?php echo ($_POST['purpose'] ?? '') == 'corporate' ? 'selected' : ''; ?>>Корпоративное мероприятие</option>
                                    <option value="private" <?php echo ($_POST['purpose'] ?? '') == 'private' ? 'selected' : ''; ?>>Частное мероприятие</option>
                                    <option value="other" <?php echo ($_POST['purpose'] ?? '') == 'other' ? 'selected' : ''; ?>>Другое</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="attendees">👥 Количество участников</label>
                                <input type="number" id="attendees" name="attendees" 
                                       class="form-control" required min="1" max="1000"
                                       value="<?php echo htmlspecialchars($_POST['attendees'] ?? '50'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_phone">📱 Контактный телефон</label>
                                <input type="tel" id="contact_phone" name="contact_phone" 
                                       class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['contact_phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">📝 Дополнительные пожелания</label>
                                <textarea id="notes" name="notes" class="form-control" rows="4"
                                          placeholder="Особые требования, оборудование и т.д."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn">
                                    <i class="fas fa-calendar-check"></i> Забронировать
                                </button>
                                <a href="venue_detail.php?id=<?php echo $venue_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Назад
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php require_once 'footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('booking_date');
            if (dateInput && !dateInput.value) {
                dateInput.value = today;
            }
            
            const phoneInput = document.getElementById('contact_phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    
                    if (value.length > 0) {
                        if (!value.startsWith('7') && !value.startsWith('8')) {
                            value = '7' + value;
                        }
                        
                        let formatted = '+7';
                        
                        if (value.length > 1) {
                            formatted += ' (' + value.substring(1, 4);
                        }
                        if (value.length >= 5) {
                            formatted += ') ' + value.substring(4, 7);
                        }
                        if (value.length >= 8) {
                            formatted += '-' + value.substring(7, 9);
                        }
                        if (value.length >= 10) {
                            formatted += '-' + value.substring(9, 11);
                        }
                        
                        e.target.value = formatted;
                    }
                });
            }
        });
    </script>
</body>
</html>
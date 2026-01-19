<?php
session_start();
// ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ
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

$error = '';

// Получаем параметры редиректа
$redirect = $_GET['redirect'] ?? '';
$venue_id = $_GET['venue_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Определяем куда перенаправить после входа
                if ($redirect === 'booking' && $venue_id) {
                    header('Location: booking.php?id=' . $venue_id);
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $error = 'Неверный email или пароль';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при входе в систему';
        }
    } else {
        $error = 'Заполните все поля';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Музыкальные площадки Москвы</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.5s ease-out;
        }
        
        .login-header {
            background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header h1 {
            margin-bottom: 10px;
            font-size: 2.2em;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 1.1em;
        }
        
        .booking-notice {
            background: #e8f4fc;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 30px;
            border-left: 4px solid #4a6fa5;
            text-align: center;
            font-size: 0.95em;
            display: <?php echo $redirect === 'booking' ? 'block' : 'none'; ?>;
        }
        
        .booking-notice i {
            color: #4a6fa5;
            margin-right: 8px;
        }
        
        .login-form {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
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
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
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
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: #4a6fa5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #3a5a80;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .register-link a {
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
            color: #3a5a80;
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-home a {
            color: #7f8c8d;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: color 0.3s;
        }
        
        .back-home a:hover {
            color: #4a6fa5;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                border-radius: 15px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🎵 Вход</h1>
            <p>Войдите в свой аккаунт</p>
        </div>
        
        <div class="booking-notice">
            <i class="fas fa-info-circle"></i>
            <strong>Чтобы забронировать площадку, необходимо войти в аккаунт</strong>
        </div>
        
        <form class="login-form" method="POST" action="">
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <strong>Ошибка:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Пароль</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-login">
                <span>🚪 Войти в аккаунт</span>
            </button>
            
            <div class="register-link">
                Нет аккаунта? <a href="register.php<?php echo $redirect === 'booking' ? '?redirect=booking&venue_id=' . $venue_id : ''; ?>">Зарегистрироваться</a>
            </div>
            
            <div class="back-home">
                <a href="index.php">
                    ← Вернуться на главную
                </a>
            </div>
        </form>
    </div>
</body>
</html>
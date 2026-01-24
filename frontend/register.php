```php
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

$errors = [];
$success = false;

$redirect = $_GET['redirect'] ?? '';
$venue_id = $_GET['venue_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username)) {
        $errors[] = 'Введите имя пользователя';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Имя пользователя должно быть не менее 3 символов';
    }
    
    if (empty($email)) {
        $errors[] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($password)) {
        $errors[] = 'Введите пароль';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }
    
    if (empty($errors)) {
        try {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->execute([$email]);
            
            if ($check_stmt->fetch()) {
                $errors[] = 'Пользователь с таким email уже существует';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                
                $insert_stmt->execute([$username, $email, $hashed_password]);
                
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                
                $success = true;
                
                if ($redirect === 'booking' && $venue_id) {
                    header('Refresh: 2; URL=booking.php?id=' . $venue_id);
                } else {
                    header('Refresh: 2; URL=index.php');
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при регистрации: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Музыкальные площадки Москвы</title>
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
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            animation: slideUp 0.5s ease-out;
        }
        
        .register-header {
            background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .register-header h1 {
            margin-bottom: 10px;
            font-size: 2.2em;
        }
        
        .register-header p {
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
        
        .register-form {
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            text-align: center;
        }
        
        .btn-register {
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
        
        .btn-register:hover {
            background: #3a5a80;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .login-link a {
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
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
        
        .password-requirements {
            margin-top: 8px;
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .requirements-list {
            list-style: none;
            padding-left: 5px;
        }
        
        .requirements-list li {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .requirement-met {
            color: #2ecc71;
        }
        
        .requirement-not-met {
            color: #e74c3c;
        }
        
        .strength-meter {
            height: 5px;
            background: #e0e0e0;
            border-radius: 3px;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            border-radius: 3px;
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
            .register-container {
                border-radius: 15px;
            }
            
            .register-header {
                padding: 30px 20px;
            }
            
            .register-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>🎵 Регистрация</h1>
            <p>Создайте новый аккаунт</p>
        </div>
        
        <div class="booking-notice">
            <i class="fas fa-info-circle"></i>
            <strong>Зарегистрируйтесь, чтобы забронировать площадку</strong>
        </div>
        
        <form class="register-form" method="POST" action="">
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
            
            <?php if ($success): ?>
                <div class="success-message">
                    <strong>✅ Регистрация успешна!</strong>
                    <p>Добро пожаловать, <?php echo htmlspecialchars($username); ?>!</p>
                    <p>Вы будете перенаправлены <?php echo $redirect === 'booking' ? 'на страницу бронирования' : 'на главную страницу'; ?> через 2 секунды...</p>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">👤 Имя пользователя</label>
                <input type="text" id="username" name="username" class="form-control" required 
                       <?php echo $success ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" class="form-control" required 
                       <?php echo $success ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Пароль</label>
                <input type="password" id="password" name="password" class="form-control" required 
                       <?php echo $success ? 'disabled' : ''; ?>
                       oninput="checkPasswordStrength()">
                <div class="strength-meter">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <div class="password-requirements">
                    <ul class="requirements-list" id="requirementsList">
                        <li id="reqLength">
                            <span class="requirement-not-met">○</span>
                            <span>Не менее 6 символов</span>
                        </li>
                        <li id="reqLetter">
                            <span class="requirement-not-met">○</span>
                            <span>Содержит буквы</span>
                        </li>
                        <li id="reqNumber">
                            <span class="requirement-not-met">○</span>
                            <span>Содержит цифры</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">🔒 Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                       <?php echo $success ? 'disabled' : ''; ?>
                       oninput="checkPasswordMatch()">
                <div class="password-requirements">
                    <p id="passwordMatchMessage" style="margin-top: 5px; font-size: 0.9em;"></p>
                </div>
            </div>
            
            <?php if (!$success): ?>
                <button type="submit" class="btn-register">
                    <span>📝 Зарегистрироваться</span>
                </button>
            <?php endif; ?>
            
            <div class="login-link">
                Уже есть аккаунт? <a href="login.php<?php echo $redirect === 'booking' ? '?redirect=booking&venue_id=' . $venue_id : ''; ?>">Войти</a>
            </div>
            
            <div class="back-home">
                <a href="index.php">
                    ← Вернуться на главную
                </a>
            </div>
        </form>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthFill = document.getElementById('strengthFill');
            const reqLength = document.getElementById('reqLength');
            const reqLetter = document.getElementById('reqLetter');
            const reqNumber = document.getElementById('reqNumber');
            
            let strength = 0;
            let color = '#e74c3c';
            
            if (password.length >= 6) {
                strength += 33;
                reqLength.querySelector('span').className = 'requirement-met';
                reqLength.querySelector('span').textContent = '✓';
            } else {
                reqLength.querySelector('span').className = 'requirement-not-met';
                reqLength.querySelector('span').textContent = '○';
            }
            
            if (/[a-zA-Z]/.test(password)) {
                strength += 33;
                reqLetter.querySelector('span').className = 'requirement-met';
                reqLetter.querySelector('span').textContent = '✓';
            } else {
                reqLetter.querySelector('span').className = 'requirement-not-met';
                reqLetter.querySelector('span').textContent = '○';
            }
            
            if (/\d/.test(password)) {
                strength += 34;
                reqNumber.querySelector('span').className = 'requirement-met';
                reqNumber.querySelector('span').textContent = '✓';
            } else {
                reqNumber.querySelector('span').className = 'requirement-not-met';
                reqNumber.querySelector('span').textContent = '○';
            }
            
            if (strength >= 66) {
                color = '#2ecc71';
            } else if (strength >= 33) {
                color = '#f39c12';
            }
            
            strengthFill.style.width = strength + '%';
            strengthFill.style.background = color;
            
            checkPasswordMatch();
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const messageElement = document.getElementById('passwordMatchMessage');
            
            if (!confirmPassword) {
                messageElement.textContent = '';
                messageElement.style.color = '';
                return;
            }
            
            if (password === confirmPassword) {
                messageElement.textContent = '✓ Пароли совпадают';
                messageElement.style.color = '#2ecc71';
            } else {
                messageElement.textContent = '✗ Пароли не совпадают';
                messageElement.style.color = '#e74c3c';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength();
            checkPasswordMatch();
        });
    </script>
</body>
</html>
```
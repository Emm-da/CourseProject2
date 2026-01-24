<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Музыкальные площадки Москвы'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Arial', sans-serif; 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6fa5 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }
        
        .logo i {
            font-size: 2em;
            color: #4a6fa5;
            background: white;
            padding: 10px;
            border-radius: 50%;
        }
        
        .logo h1 {
            font-size: 1.5em;
            font-weight: 600;
        }
        
        .navbar ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            padding: 8px 15px;
            border-radius: 5px;
        }
        
        .navbar a:hover {
            color: #4a6fa5;
            background: rgba(255,255,255,0.1);
        }
        
        .navbar a.active {
            background: rgba(255,255,255,0.15);
            color: #4a6fa5;
        }
        
        main {
            padding: 60px 0;
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .header .container {
                flex-direction: column;
                gap: 20px;
            }
            
            .navbar ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-music"></i>
                <h1>Музыкальные площадки Москвы</h1>
            </a>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-home"></i> Главная
                    </a></li>
                    <li><a href="search.php" <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-search"></i> Поиск
                    </a></li>
                    <li><a href="recommendations.php" <?php echo basename($_SERVER['PHP_SELF']) == 'recommendations.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-star"></i> Рекомендации
                    </a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="my_bookings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'my_bookings.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-calendar-alt"></i> Бронирования
            </a></li>
            
        <?php else: ?>
            
        <?php endif; ?>
                    <li><a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-info-circle"></i> О проекте
                    </a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Войти</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
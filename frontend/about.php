<?php
// about.php - Страница "О проекте"
$page_title = 'О проекте - Музыкальные площадки Москвы';
require_once 'header.php';
?>

<!-- Стили только для контента страницы -->
<style>
    /* Стили для страницы "О проекте" */
    .hero-section {
        background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(74, 111, 165, 0.9)), 
                    url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
        padding: 100px 20px;
        border-radius: 10px;
        margin-bottom: 60px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .hero-section h1 {
        font-size: 3.5em;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero-section p {
        font-size: 1.2em;
        max-width: 700px;
        margin: 0 auto 30px;
        opacity: 0.9;
    }
    
    /* Секции */
    .section {
        background: white;
        padding: 50px;
        border-radius: 10px;
        margin-bottom: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .section:hover {
        transform: translateY(-5px);
    }
    
    .section h2 {
        color: #2c3e50;
        font-size: 2em;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .section h2 i {
        color: #4a6fa5;
    }
    
    .section p {
        font-size: 1.1em;
        margin-bottom: 20px;
        color: #555;
    }
    
    /* Цели проекта */
    .goals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .goal-card {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        transition: all 0.3s;
        border-left: 4px solid #4a6fa5;
    }
    
    .goal-card:hover {
        background: #e9ecef;
        transform: translateY(-5px);
    }
    
    .goal-card i {
        font-size: 2.5em;
        color: #4a6fa5;
        margin-bottom: 20px;
    }
    
    .goal-card h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.3em;
    }
    
    /* Статистика */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .stat-card {
        text-align: center;
        padding: 25px;
        background: linear-gradient(135deg, #4a6fa5, #2c3e50);
        color: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card .number {
        font-size: 3em;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .stat-card .label {
        font-size: 1.1em;
        opacity: 0.9;
    }
    
    /* Команда */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .team-card {
        background: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
        text-align: center;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .team-photo-container {
        width: 100%;
        height: 250px;
        overflow: hidden;
        position: relative;
        background: #e9ecef;
    }
    
    .team-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .team-card:hover img {
        transform: scale(1.05);
    }
    
    .team-card .info {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .team-card h3 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 1.2em;
    }
    
    .team-card p {
        color: #666;
        font-size: 0.95em;
        margin-bottom: 15px;
    }
    
    /* Технологии */
    .tech-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin-top: 30px;
    }
    
    .tech-item {
        background: #f8f9fa;
        padding: 20px 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s;
    }
    
    .tech-item:hover {
        background: #e9ecef;
        transform: scale(1.05);
    }
    
    .tech-item i {
        font-size: 2em;
        color: #4a6fa5;
    }
    
    /* Призыв к действию */
    .cta-section {
        background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
        color: white;
        text-align: center;
        padding: 70px 20px;
        border-radius: 10px;
        margin-top: 60px;
    }
    
    .cta-section h2 {
        color: white;
        margin-bottom: 20px;
    }
    
    .cta-section p {
        max-width: 700px;
        margin: 0 auto 30px;
        font-size: 1.1em;
        opacity: 0.9;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 35px;
        background: white;
        color: #4a6fa5;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1.1em;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn:hover {
        background: #f8f9fa;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .hero-section {
            padding: 60px 20px;
        }
        
        .hero-section h1 {
            font-size: 2.5em;
        }
        
        .section {
            padding: 30px;
        }
        
        .section h2 {
            font-size: 1.5em;
        }
        
        .goals-grid,
        .stats-grid,
        .team-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Анимации */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.8s ease-out;
    }
</style>

<main>
    <div class="container">
        <!-- Герой-секция -->
        <section class="hero-section fade-in">
            <h1>О проекте</h1>
            <p>Мы создаем единое информационное пространство для музыкантов, организаторов мероприятий и всех, кто ищет идеальную площадку для своего выступления в Москве</p>
            <a href="search.php" class="btn"><i class="fas fa-search"></i> Найти площадку</a>
        </section>

        <!-- О проекте -->
        <section class="section fade-in">
            <h2><i class="fas fa-info-circle"></i> О проекте</h2>
            <p><strong>Музыкальные площадки Москвы</strong> — это инновационная платформа, созданная для упрощения поиска и бронирования музыкальных площадок столицы. Наш проект объединяет информацию о концертных площадках, террасах и открытых пространствах Москвы.</p>
            <p>Мы стремимся сделать процесс организации мероприятий максимально простым и прозрачным, предоставляя полную информацию о площадках, их техническом оснащении и доступности.</p>
        </section>

        <!-- Наши цели -->
        <section class="section fade-in">
            <h2><i class="fas fa-bullseye"></i> Наши цели</h2>
            <div class="goals-grid">
                <div class="goal-card">
                    <i class="fas fa-search"></i>
                    <h3>Упростить поиск</h3>
                    <p>Создать удобный и интуитивно понятный инструмент для поиска подходящих музыкальных площадок</p>
                </div>
                <div class="goal-card">
                    <i class="fas fa-database"></i>
                    <h3>Централизовать информацию</h3>
                    <p>Собрать актуальную и полную информацию о всех музыкальных площадках Москвы в одном месте</p>
                </div>
                <div class="goal-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Создать сообщество</h3>
                    <p>Объединить музыкантов, организаторов и владельцев площадок для эффективного взаимодействия</p>
                </div>
            </div>
        </section>

        <!-- Статистика -->
        <section class="section fade-in">
            <h2><i class="fas fa-chart-line"></i> В цифрах</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">40+</div>
                    <div class="label">Площадок</div>
                </div>
                <div class="stat-card">
                    <div class="number">16</div>
                    <div class="label">Районов Москвы</div>
                </div>
                <div class="stat-card">
                    <div class="number">40+</div>
                    <div class="label">Фотографий</div>
                </div>
                <div class="stat-card">
                    <div class="number">24/7</div>
                    <div class="label">Доступность</div>
                </div>
            </div>
        </section>

        <!-- Технологии -->
        <section class="section fade-in">
            <h2><i class="fas fa-code"></i> Технологии</h2>
            <p>Наш проект построен на современных технологиях, обеспечивающих быстродействие, безопасность и удобство использования:</p>
            <div class="tech-grid">
                <div class="tech-item">
                    <i class="fab fa-php"></i>
                    <span>PHP 8+</span>
                </div>
                <div class="tech-item">
                    <i class="fas fa-database"></i>
                    <span>MySQL</span>
                </div>
                <div class="tech-item">
                    <i class="fab fa-js"></i>
                    <span>JavaScript</span>
                </div>
                <div class="tech-item">
                    <i class="fab fa-html5"></i>
                    <span>HTML5 & CSS3</span>
                </div>
                <div class="tech-item">
                    <i class="fab fa-font-awesome"></i>
                    <span>Font Awesome</span>
                </div>
            </div>
        </section>

        <!-- Команда -->
        <section class="section fade-in">
            <h2><i class="fas fa-users"></i> Команда проекта</h2>
            <p>Над проектом работает человек, который любит музыку и технологии:</p>
            <div class="team-grid">
                <div class="team-card">
                    <img src="images/photo_2025-11-23_20-33-35.jpg" alt="Руководитель проекта">
                    <div class="info">
                        <h3>Анна Жгутова</h3>
                        <p>Руководитель проекта</p>
                    </div>
                </div>
                <div class="team-card">
                    <img src="images/photo_2024-09-02_21-24-42.jpg" alt="Разработчик">
                    <div class="info">
                        <h3>Анна Жгутова</h3>
                        <p>Full-stack разработчик</p>
                    </div>
                </div>   
            </div>
        </section>

        <!-- Призыв к действию -->
        <section class="cta-section fade-in">
            <h2>Готовы найти идеальную площадку?</h2>
            <p>Начните поиск прямо сейчас и найдите площадку, которая подходит именно вам. Это бесплатно и займет всего несколько минут!</p>
            <a href="search.php" class="btn"><i class="fas fa-search"></i> Начать поиск</a>
        </section>
    </div>
</main>

<?php require_once 'footer.php'; ?>

<script>
    // Анимация при прокрутке
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Наблюдаем за всеми секциями
        document.querySelectorAll('.section').forEach(section => {
            observer.observe(section);
        });

        // Плавная прокрутка для якорных ссылок
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
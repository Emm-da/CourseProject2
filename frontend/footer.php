<?php
?>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Музыкальные площадки Москвы</h3>
                    <p>Ваш гид по музыкальным площадкам столицы</p>
                    <p><i class="fas fa-map-marker-alt"></i> Москва, ул. Музыкальная, 123</p>
                    <p><i class="fas fa-phone"></i> +7 (495) 123-45-67</p>
                    <p><i class="fas fa-envelope"></i> info@music-venues.ru</p>
                </div>
                
                <div class="footer-section">
                    <h3>Быстрые ссылки</h3>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Главная</a></li>
                        <li><a href="search.php"><i class="fas fa-search"></i> Поиск площадок</a></li>
                        <li><a href="recommendations.php"><i class="fas fa-star"></i> Рекомендации</a></li>
                        <li><a href="about.php"><i class="fas fa-info-circle"></i> О проекте</a></li>
                    </ul>
                </div>
                
                
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Музыкальные площадки Москвы. Все права защищены.</p>
                <p class="disclaimer">Используются открытые данные Правительства Москвы</p>
            </div>
        </div>
    </footer>
    
    <style>
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            padding: 50px 0 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-section h3 {
            color: white;
            margin-bottom: 25px;
            font-size: 1.3em;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #4a6fa5;
        }
        
        .footer-section p {
            color: #b0b0b0;
            margin-bottom: 12px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .footer-section i {
            color: #4a6fa5;
            width: 20px;
            text-align: center;
            margin-top: 3px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 15px;
        }
        
        .footer-links a {
            color: #b0b0b0;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            padding: 5px 0;
        }
        
        .footer-links a:hover {
            color: #4a6fa5;
            transform: translateX(5px);
        }
        
        .tech-icons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .tech-icon {
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .tech-icon:hover {
            background: rgba(74, 111, 165, 0.2);
            transform: translateY(-2px);
        }
        
        .tech-icon i {
            font-size: 1.1em;
        }
        
        .project-info {
            font-size: 0.9em;
            color: #7f8c8d !important;
            margin-top: 15px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        .disclaimer {
            font-size: 0.85em;
            color: #666;
            margin-top: 10px;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .footer {
                padding: 40px 0 20px;
            }
            
            .footer-content {
                gap: 30px;
            }
            
            .footer-section {
                text-align: center;
            }
            
            .footer-section p {
                justify-content: center;
            }
            
            .footer-links a {
                justify-content: center;
            }
            
            .tech-icons {
                justify-content: center;
            }
        }
    </style>
    
    <script>
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
        
        document.addEventListener('DOMContentLoaded', function() {
            const yearElement = document.querySelector('.footer-bottom p:first-child');
            if (yearElement) {
                yearElement.innerHTML = yearElement.innerHTML.replace(/\d{4}/, new Date().getFullYear());
            }
        });
        
        document.querySelectorAll('.footer-links a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.color = '#4a6fa5';
            });
            link.addEventListener('mouseleave', function() {
                this.style.color = '#b0b0b0';
            });
        });
        
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#e74c3c';
                    } else {
                        field.style.borderColor = '';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Пожалуйста, заполните все обязательные поля');
                }
            });
        });
    </script>
</body>
</html>
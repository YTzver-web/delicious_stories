<?php
// Подключение к базе данных
$a = mysqli_connect('localhost', 'root', '', 'res') or die('Ошибка подключения: ' . mysqli_connect_error());
session_start();

// Функция для безопасного вывода
function safeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Определяем текущую страницу
$currentPage = basename($_SERVER['PHP_SELF']);

// Проверяем авторизацию пользователя
$isAuthenticated = isset($_SESSION['user_id']);

// Массив пунктов меню
$menuItems = [
    'index.php' => 'Главная',
    'catalog.php' => 'Меню',
    'cart.php' => 'О нас',
];

if ($isAuthenticated) {
    $menuItems['profile.php'] = 'Профиль';
    $menuItems['logout.php'] = 'Выход';
} else {
    $menuItems['authoriz.php'] = 'Вход';
    $menuItems['reg.php'] = 'Регистрация';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - Вкусные истории</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/aaa.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/footer.css">
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="index.php"><img src="img/chef.png" alt="Логотип"></a>
        </div>
        <div class="title">
            <b>Вкусные истории</b>
        </div>
        <div class="icons">
            <div class="authorization">
                <a href="authoriz.php"><img src="img/ЛК.svg" alt="Личный кабинет"></a>
            </div>
            
            <div class="menu-container">
                <img src="img/icons8-меню 1.svg" class="menu-icon" alt="Меню" aria-label="Открыть меню">
                <div class="dropdown-menu">
                    <?php foreach ($menuItems as $url => $title): 
                        $isActive = ($currentPage == $url) ? 'active' : '';
                    ?>
                        <a href="<?= safeOutput($url) ?>" class="<?= $isActive ?>">
                            <?= safeOutput($title) ?>
                        </a>
                    <?php endforeach; ?>
                    
                    <?php if ($isAuthenticated): ?>
                        <div class="user-info">
                            <span><?= safeOutput($_SESSION['login'] ?? 'Пользователь') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="about-container">
        <h1>О нашей компании</h1>
        
        <div class="about-content">
            <div class="about-text">
                <h2>Наша история</h2>
                <p>"Вкусные истории" - это больше чем просто кулинарный проект. Начав с маленького домашнего производства в 2015 году, мы выросли в сообщество ценителей качественной еды и теплой атмосферы.</p>
                
                <h2>Наши преимущества</h2>
                <ul>
                    <li>Свежие продукты от местных поставщиков</li>
                    <li>Авторские рецепты наших шеф-поваров</li>
                    <li>Экологичная упаковка</li>
                </ul>
                
                <h2>Контакты</h2>
                <p><strong>Телефон:</strong> +7 (960) 953-43-21</p>
                <p><strong>Email:</strong> denis.nonko@mail.ru</p>
                <p><strong>Режим работы:</strong> с 8:00 до 22:00 без выходных</p>
            </div>
            
            <div class="about-map">
                <h2>Наше местоположение</h2>
                <div class="map-container">
                    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A04a46d280e6efbe30e813a78627602f3396fe2fe2fc6ad681c5322368a05b95c&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
                </div>
                <p class="address">г. Барнаул, ул. Кленова д. 10</p>
            </div>
        </div>
    </div>
<footer class="site-footer">
        <div class="footer-content">
            <div class="footer-contacts">
                <p>Email: <a href="mailto:denis.nonko@mail.ru">denis.nonko@mail.ru</a></p>
                <p>Телефон: +8-(960)-(953)-43-21</p>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2025 Вкусные истории. Все права защищены.</p>
            </div>
        </div>
</footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuIcon = document.querySelector('.menu-icon');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
            if (menuIcon && dropdownMenu) {
                menuIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('active');
                });
                
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.menu-container')) {
                        dropdownMenu.classList.remove('active');
                    }
                });
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdownMenu.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>
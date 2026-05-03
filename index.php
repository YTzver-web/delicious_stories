<?php
session_start();
// Подключение к БД
require_once 'connect.php';

// Проверка авторизации и перенаправление
if (!isset($_SESSION['id'])) {
    header("Location: authoriz.php");
    exit();
}


function safeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

$currentPage = basename($_SERVER['PHP_SELF']);
$isAuthenticated = isset($_SESSION['id']);

$menuItems = [
    'index.php' => 'Главная',
    'catalog.php' => 'Меню',
    'cart.php' => 'О нас'
];

if ($isAuthenticated) {
    // Убрали пункт "Профиль"
    $menuItems['logout.php'] = 'Выход';
} else {
    $menuItems['authoriz.php'] = 'Вход';
    $menuItems['reg.php'] = 'Регистрация';
}

// Получаем блюда из БД для слайдера
$dishes_query = "SELECT id_tovar, name, image_path, price FROM tovars ORDER BY RAND() LIMIT 10";
$dishes_result = mysqli_query($db, $dishes_query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вкусные истории</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/aaa.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <style> 
        /* Остальные стили остаются без изменений */
        .promo-slider {
            position: relative;
            margin: 50px auto;
            max-width: 1200px;
            padding: 0 20px;
        }
        /* Стили для слайдера */
        .promo-slider {
            position: relative;
            margin: 50px auto;
            max-width: 1200px;
            padding: 0 20px;
        }
        .promo-banner {
            background: #D53322;
            color: white;
            padding: 15px 30px;
            border-radius: 8px 8px 0 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .swiper {
            width: 100%;
            height: 350px;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .swiper-slide {
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            box-sizing: border-box;
        }
        .slide-image {
            height: 180px;
            overflow: hidden;
            border-radius: 8px;
        }
        .slide-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .swiper-slide:hover .slide-image img {
            transform: scale(1.05);
        }
        .slide-info {
            padding: 15px 0;
        }
        .slide-title {
            font-size: 18px;
            margin-bottom: 8px;
            color: #333;
        }
        .slide-price {
            font-weight: bold;
            color: #D53322;
            font-size: 20px;
        }
        .slide-old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 16px;
            margin-left: 8px;
        }
        .swiper-button-next, .swiper-button-prev {
            color: #D53322;
            background: rgba(255,255,255,0.8);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .swiper-button-next::after, .swiper-button-prev::after {
            font-size: 20px;
        }
    </style>
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
            <a href="authoriz.php">
                <img src="img/ЛК.svg" alt="Личный кабинет">
            </a>
        </div>
        
        <div class="menu-container">
            <img src="img/icons8-меню 1.svg" class="menu-icon" alt="Меню">
            <div class="dropdown-menu">
                <?php foreach ($menuItems as $url => $title): 
                    $isActive = ($currentPage == $url) ? 'active' : '';
                ?>
                    <a href="<?= safeOutput($url) ?>" class="<?= $isActive ?>">
                        <?= safeOutput($title) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

    <!-- Герой-секция -->
    <section class="hero">
        <div class="hero-content">
            <h1>Летнее настроение на каждой тарелке</h1>
            <p>Свежие фермерские продукты, средиземноморские рецепты и прохладные авторские напитки</p>
            <a href="catalog.php" class="btn">Посмотреть меню</a>
        </div>
    </section>

    <!-- Слайдер с акционными блюдами -->
    <div class="promo-slider">
        <div class="promo-banner">
            <i class="fas fa-tag"></i> Скидки 15% на все, просто скажите на кассе промокод "ЛЕТО"!
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php while($dish = $dishes_result->fetch_assoc()): 
                    $discount_price = $dish['price'] * 0.85; // Расчет цены со скидкой
                ?>
                <div class="swiper-slide">
                    <div class="slide-image">
                        <img src="<?= safeOutput($dish['image_path']) ?>" alt="<?= safeOutput($dish['name']) ?>">
                    </div>
                    <div class="slide-info">
                        <h3 class="slide-title"><?= safeOutput($dish['name']) ?></h3>
                        <div class="slide-price">
                            <?= number_format($discount_price, 0, '', ' ') ?> ₽
                            <span class="slide-old-price"><?= number_format($dish['price'], 0, '', ' ') ?> ₽</span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <!-- Особенности -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <h3>Свежесть в каждой детали</h3>
            <p>Блюда из сезонных фермерских продуктов, рыба daily catch и ягодные десерты</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-cocktail"></i>
            </div>
            <h3>Авторские напитки</h3>
            <p>Лимонады с мятой, крафтовые смузи и вина с солнечных виноградников</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-umbrella-beach"></i>
            </div>
            <h3>Особенная атмосфера</h3>
            <p>Уютная веранда с видом на сад и живая музыка по вечерам</p>
        </div>
    </section>

    <!-- Цитата -->
    <section class="testimonial">
        <blockquote>
            "У нас лето не кончается — оно в хрусте свежего багета, 
            в аромате розмарина на гриле, в прохладе домашнего лимонада."
        </blockquote>
    </section>

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

    <!-- Подключаем Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Инициализация слайдера
        const swiper = new Swiper('.swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                },
            }
        });

        // Меню
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
            }
        });
    </script>
</body>
</html>
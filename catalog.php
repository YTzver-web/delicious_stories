<?php
require_once 'connect.php';
session_start();
// Проверка авторизации и перенаправление
if (!isset($_SESSION['id'])) {
    header("Location: authoriz.php");
    exit();
}
// Функции
function safeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Проверка админских прав
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Получаем блюда из таблицы tovars
$dishes_query = "SELECT * FROM tovars ORDER BY category, name";
$dishes_result = mysqli_query($db, $dishes_query);
$dishes_by_category = [];
while ($dish = $dishes_result->fetch_assoc()) {
    $dishes_by_category[$dish['category']][] = $dish;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$isAuthenticated = isset($_SESSION['id']);

$menuItems = [
    'index.php' => 'Главная',
    'catalog.php' => 'Меню',
    'cart.php' => 'О нас'
];

if ($isAuthenticated) {
    $menuItems['logout.php'] = 'Выход';
} else {
    $menuItems['authoriz.php'] = 'Вход';
    $menuItems['reg.php'] = 'Регистрация';
}

// Получаем уникальные категории
$categories = array_keys($dishes_by_category);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Меню ресторана</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <style>
        /* Общие стили */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Стили меню */
        .menu-category {
            margin-bottom: 50px;
        }
        .category-title {
            font-size: 28px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #D53322;
            color: #333;
        }
        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        .dish-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .dish-card:hover {
            transform: translateY(-5px);
        }
        .dish-image {
            height: 200px;
            overflow: hidden;
        }
        .dish-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .dish-card:hover .dish-image img {
            transform: scale(1.05);
        }
        .dish-info {
            padding: 20px;
        }
        .dish-name {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        .dish-desc {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .dish-price {
            font-weight: bold;
            color: #D53322;
            font-size: 18px;
        }
        
        /* Админ-панель */
        .admin-panel {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #D53322;
        }
        .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .admin-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-edit {
            background: #2196F3;
            color: white;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .btn-add {
            background: #4CAF50;
            color: white;
            margin-bottom: 20px;
        }
        
        /* Модальное окно */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
        }
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
        }
        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary {
            background: #D53322;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        /* Стили для слайдера */
        .promo-slider {
            position: relative;
            margin: 30px auto;
            max-width: 1200px;
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
        
        /* Подвал */
        .site-footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            margin-top: 50px;
            border-top: 1px solid #e9ecef;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-contacts a {
            color: #D53322;
            text-decoration: none;
        }
        .footer-contacts a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Шапка -->
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
                <img src="img/icons8-меню 1.svg" class="menu-icon" alt="Меню">
                <div class="dropdown-menu">
                    <?php foreach ($menuItems as $url => $title): 
                        $isActive = ($currentPage == $url) ? 'active' : '';
                    ?>
                        <a href="<?= safeOutput($url) ?>" class="<?= $isActive ?>"><?= safeOutput($title) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Админ-панель (только для админа) -->
        <?php if($isAdmin): ?>
        <div class="admin-panel">
            <h2>Панель управления меню</h2>
            <button class="admin-btn btn-add" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Добавить блюдо
            </button>
        </div>
        <?php endif; ?>

        <!-- Слайдер популярных блюд -->
        <div class="promo-slider">
            <div class="promo-banner">
                <i class="fas fa-fire"></i> Популярные блюда
            </div>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php 
                    $popular_query = "SELECT * FROM tovars ORDER BY RAND() LIMIT 8";
                    $popular_result = mysqli_query($db, $popular_query);
                    while($dish = $popular_result->fetch_assoc()): 
                    ?>
                    <div class="swiper-slide">
                        <div class="slide-image">
                            <img src="<?= safeOutput($dish['image_path']) ?>" alt="<?= safeOutput($dish['name']) ?>">
                        </div>
                        <div class="slide-info">
                            <h3 class="slide-title"><?= safeOutput($dish['name']) ?></h3>
                            <div class="slide-price">
                                <?= number_format($dish['price'], 0, '', ' ') ?> ₽
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <!-- Список блюд по категориям -->
        <?php foreach($dishes_by_category as $category => $dishes): ?>
        <div class="menu-category">
            <h2 class="category-title"><?= safeOutput($category) ?></h2>
            <div class="dishes-grid">
                <?php foreach($dishes as $dish): ?>
                <div class="dish-card" data-id="<?= $dish['id_tovar'] ?>">
                    <div class="dish-image">
                        <img src="<?= safeOutput($dish['image_path']) ?>" alt="<?= safeOutput($dish['name']) ?>">
                    </div>
                    <div class="dish-info">
                        <h3 class="dish-name"><?= safeOutput($dish['name']) ?></h3>
                        <p class="dish-desc"><?= safeOutput($dish['description']) ?></p>
                        <div class="dish-bottom">
                            <span class="dish-price"><?= number_format($dish['price'], 0, '', ' ') ?> ₽</span>
                            <?php if($isAdmin): ?>
                            <div class="admin-actions">
                                <button class="admin-btn btn-edit" onclick="editDish(<?= $dish['id_tovar'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="admin-btn btn-delete" onclick="deleteDish(<?= $dish['id_tovar'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Подвал -->
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

    <!-- Модальное окно добавления/редактирования (только для админа) -->
    <?php if($isAdmin): ?>
    <div id="dishModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Добавить новое блюдо</h2>
            <form id="dishForm" enctype="multipart/form-data">
                <input type="hidden" id="dishId" name="id_tovar" value="">
                
                <div class="form-group">
                    <label for="dishName">Название блюда:</label>
                    <input type="text" id="dishName" name="name" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="dishPrice">Цена (₽):</label>
                    <input type="number" id="dishPrice" name="price" min="0" step="10" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="dishCategory">Категория:</label>
                    <select id="dishCategory" name="category" required class="form-control">
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= safeOutput($cat) ?>"><?= safeOutput($cat) ?></option>
                        <?php endforeach; ?>
                        <option value="new">Новая категория...</option>
                    </select>
                    <input type="text" id="newCategory" name="new_category" class="form-control" style="display:none; margin-top:5px;" placeholder="Введите новую категорию">
                </div>
                
                <div class="form-group">
                    <label for="dishDescription">Описание:</label>
                    <textarea id="dishDescription" name="description" rows="3" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="dishImage">Изображение:</label>
                    <input type="file" id="dishImage" name="image" accept="image/*" class="form-control">
                    <small>Рекомендуемый размер: 800x600px</small>
                    <div id="imagePreview" style="margin-top:10px;"></div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

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

        <?php if($isAdmin): ?>
        // Функции для админ-панели
        let currentModalMode = 'add';
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Добавить новое блюдо';
            document.getElementById('dishForm').reset();
            document.getElementById('dishId').value = '';
            document.getElementById('imagePreview').innerHTML = '';
            currentModalMode = 'add';
            document.getElementById('dishModal').style.display = 'block';
        }
        
        function editDish(id) {
            fetch('get_dish.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('modalTitle').textContent = 'Редактировать блюдо';
                        document.getElementById('dishId').value = data.dish.id_tovar;
                        document.getElementById('dishName').value = data.dish.name;
                        document.getElementById('dishPrice').value = data.dish.price;
                        document.getElementById('dishCategory').value = data.dish.category;
                        document.getElementById('dishDescription').value = data.dish.description;
                        
                        if(data.dish.image_path) {
                            document.getElementById('imagePreview').innerHTML = 
                                `<img src="${data.dish.image_path}" style="max-width:200px; max-height:150px;">`;
                        }
                        
                        currentModalMode = 'edit';
                        document.getElementById('dishModal').style.display = 'block';
                    }
                });
        }
        
        function deleteDish(id) {
            if(confirm('Вы уверены, что хотите удалить это блюдо?')) {
                fetch('delete_dish.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id_tovar=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                });
            }
        }
        
        function closeModal() {
            document.getElementById('dishModal').style.display = 'none';
        }
        
        // Обработка формы
        document.getElementById('dishForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', currentModalMode);
            
            fetch('save_dish.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            });
        });
        
        // Обработка новой категории
        document.getElementById('dishCategory').addEventListener('change', function() {
            const newCategoryInput = document.getElementById('newCategory');
            if(this.value === 'new') {
                newCategoryInput.style.display = 'block';
                newCategoryInput.required = true;
            } else {
                newCategoryInput.style.display = 'none';
                newCategoryInput.required = false;
            }
        });
        
        // Закрытие модального окна при клике вне его
        window.addEventListener('click', function(e) {
            if(e.target === document.getElementById('dishModal')) {
                closeModal();
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
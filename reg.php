<?php
// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаем коннект к БД
require_once 'connect.php';

// Инициализация переменной для ошибок
$error = '';

// Проверяем нажата ли кнопка отправки формы
if (isset($_REQUEST['doGo'])) {
    
    // Проверка на совпадение паролей
    if ($_POST['pass'] !== $_POST['pass_rep']) {
        $error = 'Пароль не совпадает';
    }
    
    // Проверка есть ли вообще повторный пароль
    if (empty($_POST['pass_rep'])) {
        $error = 'Введите повторный пароль';
    }
    
    // Проверка есть ли пароль
    if (empty($_POST['pass'])) {
        $error = 'Введите пароль';
    }

    // Проверка есть ли email
    if (empty($_POST['email'])) {
        $error = 'Введите email';
    }

    // Проверка есть ли логин
    if (empty($_POST['login'])) {
        $error = 'Введите login';
    }

    // Если ошибок нет, то происходит регистрация 
    if (empty($error)) {
        $login = $_POST['login'];
        $email = $_POST['email'];
        // Пароль хешируется
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        
        // Добавление пользователя
        if (mysqli_query($db, "INSERT INTO users (login, email, pass) VALUES ('$login','$email','$pass')")) {       
        	echo "<script>alert('Регистрация прошла успешно, авторизируйтесь!'); window.location.href = 'authoriz.php';</script>";
       } else {
            echo 'Ошибка при добавлении пользователя: ' . mysqli_error($db);
        }
    } else {
        // Если ошибка есть, то выводить её 
        echo $error; 
    }
}
session_start();

// Функция для безопасного вывода
function safeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Определяем текущую страницу
$currentPage = basename($_SERVER['PHP_SELF']);

// Проверяем авторизацию пользователя
$isAuthenticated = isset($_SESSION['id']);

// Массив пунктов меню
$menuItems = [
    'index.php' => 'Главная',
    'catalog.php' => 'Меню',
    'cart.php' => 'О нас'
];

// Добавляем пункты в зависимости от авторизации
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
    <title>Регистрация</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/footer.css">
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="img/chef.png" alt="Логотип">
        </div>
        <div class="title">
            <b>Вкусные истории</b>
        </div>
        <div class="icons">
            <div class="authorization">
                <a href="authoriz.php"><img src="img/ЛК.svg" alt="Личный кабинет"></a>
            </div>
        </div>
    </div>
        <form class="form" action="" method="POST">

            <center><input type="text" placeholder="ФИО" class="form__input" id="" name="login" required><br>
          
            <input type="email" placeholder="Email" class="form__input" id="" name="email" required><br>
          
            <input type="password" placeholder="Пароль" class="form__input" id="" name="pass" required><br>
          
            <input type="password" placeholder="Повторите пароль" class="form__input" name="pass_rep" id="" required><br>
            
            <div class="checkbox-container">
                <input class="cb" type="checkbox" id="personal-data" name="personal_data" required>
                <label for="personal-data">Я согласен на обработку персональных данных</label>
            </div>               

          <input type="submit" value="Зарегистрироваться" name="doGo">
        </form>
    
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
</body>
</html>
<script>
        // Скрипт для работы выпадающего меню
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
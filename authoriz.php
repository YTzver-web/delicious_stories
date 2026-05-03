<?php
session_start();

// Если пользователь уже авторизован - перенаправляем на главную
if (isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// Подключение БД
require_once 'connect.php';


// Проверка нажата ли кнопка отправки формы
if (isset($_POST['doGo'])) {
    $error = '';
 
    // Проверяем логин
    if (empty($_POST['login'])) {
        $error = 'Введите логин';
    }
    // Проверяем пароль
    elseif (empty($_POST['pass'])) {
        $error = 'Введите пароль';
    }
 
    // Если нет ошибок
    if (empty($error)) {
        $login = trim($_POST['login']);
        $pass = $_POST['pass'];
 
        // Используем подготовленные выражения для защиты от SQL-инъекций
        $stmt = mysqli_prepare($db, "SELECT `pass`, `id`, `is_admin` FROM `users` WHERE `login` = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $login);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($pass, $row['pass'])) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['login'] = $login;
                $_SESSION['is_admin'] = $row['is_admin'];
                
                echo "<script>alert('Вы вошли!'); window.location.href = 'index.php';</script>";
                exit;
            } else {
                $error = "Неверный пароль";
            }
        } else {
            $error = "Пользователь с таким логином не найден";
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Выводим ошибку, если есть
    if (!empty($error)) {
        echo '<div class="error-message">'.htmlspecialchars($error).'</div>';
    }
}

// Функция для безопасного вывода
function safeOutput($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
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
    <title>Авторизация</title>
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
            <a href="<?= $isAuthenticated ? 'profile.php' : 'authoriz.php' ?>">
                <img src="img/ЛК.svg" alt="Личный кабинет">
            </a>
        </div>
    </div>
</div>
    
    <form class="form2" method="POST" action="auth_process.php">
        <label>Введите ваш ФИО:</label>
        <input type="text" class="form2__input" name="login" value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>"><br>
        
        <label>Введите ваш пароль</label>
        <input type="password" class="form2__input" name="pass"><br>
        
        <input class="enter" type="submit" value="Войти" name="doGo">
        
        <div class="url_reg">
            <center>Нет аккаунта?<br>
            <center><a href="reg.php" class="rega">Зарегистрироваться</a>
        </div>
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
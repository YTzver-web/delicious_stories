<?php
session_start();

require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $pass = $_POST['pass'];

    // Поиск пользователя в базе
    $stmt = $db->prepare("SELECT id, pass, is_admin FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($pass, $user['pass'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: index.php");
            exit();
        }
    }

    // Если авторизация не удалась
    $_SESSION['auth_error'] = "Неверный логин или пароль";
    header("Location: authoriz.php");
    exit();
}
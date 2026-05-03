<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

if (!isset($_POST['id_tovar'])) {
    echo json_encode(['success' => false, 'message' => 'ID не указан']);
    exit;
}

$id = (int)$_POST['id_tovar'];
$query = "DELETE FROM tovars WHERE id_tovar = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении']);
}
?>
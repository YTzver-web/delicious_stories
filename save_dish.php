<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id_tovar'] ?? 0;

$name = mysqli_real_escape_string($db, $_POST['name']);
$price = (float)$_POST['price'];
$category = $_POST['category'] == 'new' 
    ? mysqli_real_escape_string($db, $_POST['new_category'])
    : mysqli_real_escape_string($db, $_POST['category']);
$description = mysqli_real_escape_string($db, $_POST['description']);

// Обработка изображения
$image_path = null;
if (!empty($_FILES['image']['tmp_name'])) {
    $upload_dir = 'img/eda/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename);
    $image_path = $upload_dir . $filename;
}

if ($action == 'add') {
    $query = "INSERT INTO tovars (name, price, description, image_path, category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sdsss", $name, $price, $description, $image_path, $category);
} else {
    if ($image_path) {
        $query = "UPDATE tovars SET name=?, price=?, description=?, image_path=?, category=? WHERE id_tovar=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sdsssi", $name, $price, $description, $image_path, $category, $id);
    } else {
        $query = "UPDATE tovars SET name=?, price=?, description=?, category=? WHERE id_tovar=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sdssi", $name, $price, $description, $category, $id);
    }
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $db->error]);
}
?>
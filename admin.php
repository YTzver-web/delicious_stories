<?php
require_once 'connect.php';

// Безопасный старт сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка прав администратора с JSON-ответом
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false, 
        'message' => 'Доступ запрещен',
        'error_code' => 403
    ]));
}

// Обработка разных действий
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addDish();
            break;
        case 'edit':
            editDish();
            break;
        case 'delete':
            deleteDish();
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Неизвестное действие',
                'action' => $action
            ]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка сервера: ' . $e->getMessage(),
        'error_code' => 500
    ]);
}

function addDish() {
    global $a;
    
    // Валидация обязательных полей
    $required = ['name', 'description', 'price', 'category'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Поле {$field} обязательно для заполнения");
        }
    }
    
    // Экранирование данных
    $name = mysqli_real_escape_string($a, $_POST['name']);
    $description = mysqli_real_escape_string($a, $_POST['description']);
    $price = (float)$_POST['price'];
    $category = $_POST['category'] == 'new' 
        ? mysqli_real_escape_string($a, $_POST['new_category'])
        : mysqli_real_escape_string($a, $_POST['category']);
    
    // Обработка загрузки изображения
    $imagePath = handleImageUpload();
    
    // Подготовленный запрос для безопасности
    $stmt = $a->prepare("INSERT INTO tovars (name, description, price, category, image_path) 
                         VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $description, $price, $category, $imagePath);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        throw new Exception("Ошибка базы данных: " . $stmt->error);
    }
}

function editDish() {
    global $a;
    
    // Проверка ID блюда
    if (empty($_POST['id'])) {
        throw new Exception("ID блюда не указан");
    }
    $id = (int)$_POST['id'];
    
    // Получаем текущие данные блюда
    $stmt = $a->prepare("SELECT * FROM tovars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $dish = $stmt->get_result()->fetch_assoc();
    
    if (!$dish) {
        throw new Exception("Блюдо не найдено");
    }
    
    // Обновляем только переданные поля
    $name = isset($_POST['name']) ? mysqli_real_escape_string($a, $_POST['name']) : $dish['name'];
    $description = isset($_POST['description']) ? mysqli_real_escape_string($a, $_POST['description']) : $dish['description'];
    $price = isset($_POST['price']) ? (float)$_POST['price'] : $dish['price'];
    $category = isset($_POST['category']) 
        ? ($_POST['category'] == 'new' 
            ? mysqli_real_escape_string($a, $_POST['new_category'])
            : mysqli_real_escape_string($a, $_POST['category']))
        : $dish['category'];
    
    // Обработка изображения (если загружено новое)
    $imagePath = isset($_FILES['image']) ? handleImageUpload() : $dish['image_path'];
    
    // Подготовленный запрос для обновления
    $stmt = $a->prepare("UPDATE tovars SET 
                        name = ?, 
                        description = ?, 
                        price = ?, 
                        category = ?, 
                        image_path = ?
                        WHERE id = ?");
    $stmt->bind_param("ssdssi", $name, $description, $price, $category, $imagePath, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'affected_rows' => $stmt->affected_rows]);
    } else {
        throw new Exception("Ошибка обновления: " . $stmt->error);
    }
}

function deleteDish() {
    global $a;
    
    if (empty($_POST['id'])) {
        throw new Exception("ID блюда не указан");
    }
    $id = (int)$_POST['id'];
    
    // Подготовленный запрос для безопасности
    $stmt = $a->prepare("DELETE FROM tovars WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'affected_rows' => $stmt->affected_rows
        ]);
    } else {
        throw new Exception("Ошибка удаления: " . $stmt->error);
    }
}

function handleImageUpload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        return '';
    }
    
    $uploadDir = 'img/eda/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Проверка типа файла
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
    
    if (!in_array($mime, $allowedTypes)) {
        throw new Exception("Допустимы только изображения JPG, PNG или WebP");
    }
    
    // Генерация уникального имени
    $ext = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/webp' => '.webp'
    ][$mime];
    
    $fileName = uniqid() . $ext;
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    
    throw new Exception("Ошибка загрузки изображения");
}
?>
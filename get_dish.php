<?php
require_once 'connect.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID не указан']);
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM tovars WHERE id_tovar = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Блюдо не найдено']);
    exit;
}

echo json_encode(['success' => true, 'dish' => $result->fetch_assoc()]);
?>
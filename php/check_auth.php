<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['authenticated' => false]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 1) {
        echo json_encode(['authenticated' => true]);
    } else {
        session_destroy();
        echo json_encode(['authenticated' => false]);
    }
} catch(PDOException $e) {
    echo json_encode(['authenticated' => false, 'error' => 'Error en el servidor']);
}
?>
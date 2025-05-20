<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

try {
    // Citas próximas (desde hoy en adelante)
    $stmt = $conn->prepare("SELECT id, DATE_FORMAT(fecha, '%Y-%m-%d') as fecha, 
                          TIME_FORMAT(hora, '%H:%i') as hora, motivo, estado 
                          FROM citas 
                          WHERE usuario_id = :user_id AND fecha >= CURDATE()
                          ORDER BY fecha, hora");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $proximas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Citas anteriores (antes de hoy)
    $stmt = $conn->prepare("SELECT id, DATE_FORMAT(fecha, '%Y-%m-%d') as fecha, 
                          TIME_FORMAT(hora, '%H:%i') as hora, motivo, estado 
                          FROM citas 
                          WHERE usuario_id = :user_id AND fecha < CURDATE()
                          ORDER BY fecha DESC, hora DESC");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $anteriores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'proximas' => $proximas,
        'anteriores' => $anteriores
    ]);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
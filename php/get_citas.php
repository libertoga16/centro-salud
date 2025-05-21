<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de cita no proporcionado']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT c.id, DATE_FORMAT(c.fecha, '%Y-%m-%d') as fecha, 
                          TIME_FORMAT(c.hora, '%H:%i') as hora, c.motivo, c.doctor_id,
                          CONCAT(d.nombre, ' ', d.apellido) as doctor_nombre
                          FROM citas c
                          JOIN doctores d ON c.doctor_id = d.id
                          WHERE c.id = :id AND c.usuario_id = :usuario_id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 1) {
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($cita);
    } else {
        echo json_encode(['error' => 'Cita no encontrada']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
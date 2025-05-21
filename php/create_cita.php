<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['fecha']) || !isset($data['hora']) || !isset($data['motivo']) || !isset($data['doctor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

try {
    // Verificar disponibilidad del doctor
    $stmt = $conn->prepare("SELECT id FROM citas WHERE fecha = :fecha AND hora = :hora AND doctor_id = :doctor_id");
    $stmt->bindParam(':fecha', $data['fecha']);
    $stmt->bindParam(':hora', $data['hora']);
    $stmt->bindParam(':doctor_id', $data['doctor_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'El doctor ya tiene una cita en ese horario']);
        exit();
    }
    
    // Crear la cita
    $stmt = $conn->prepare("INSERT INTO citas (usuario_id, doctor_id, fecha, hora, motivo) 
                           VALUES (:usuario_id, :doctor_id, :fecha, :hora, :motivo)");
    $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
    $stmt->bindParam(':doctor_id', $data['doctor_id']);
    $stmt->bindParam(':fecha', $data['fecha']);
    $stmt->bindParam(':hora', $data['hora']);
    $stmt->bindParam(':motivo', $data['motivo']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cita creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear cita']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
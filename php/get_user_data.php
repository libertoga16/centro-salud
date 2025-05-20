<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT nombre, apellido, telefono, email, localidad, calle, codigo_postal 
                           FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 1) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($userData);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
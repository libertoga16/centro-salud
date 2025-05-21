<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT id, nombre, apellido, especialidad, foto FROM doctores");
    $doctores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($doctores);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error al obtener lista de doctores']);
}
?>
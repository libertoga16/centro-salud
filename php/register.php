<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $localidad = $_POST['localidad'];
    $calle = $_POST['calle'];
    $codigo_postal = $_POST['codigo_postal'];

    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, telefono, email, password, localidad, calle, codigo_postal) 
                               VALUES (:nombre, :apellido, :telefono, :email, :password, :localidad, :calle, :codigo_postal)");
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':localidad', $localidad);
        $stmt->bindParam(':calle', $calle);
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        
        if ($stmt->execute()) {
            session_start();
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $nombre . ' ' . $apellido;
            
            header('Location: ../dashboard.html');
            exit();
        } else {
            header('Location: ../register.html?error=Error al registrar');
            exit();
        }
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            header('Location: ../register.html?error=Email ya registrado');
        } else {
            header('Location: ../register.html?error=Error en el servidor');
        }
        exit();
    }
} else {
    header('Location: ../register.html');
    exit();
}
?>
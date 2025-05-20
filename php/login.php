<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
                
                header('Location: ../dashboard.html');
                exit();
            } else {
                header('Location: ../login.html?error=Credenciales incorrectas');
                exit();
            }
        } else {
            header('Location: ../login.html?error=Credenciales incorrectas');
            exit();
        }
    } catch(PDOException $e) {
        header('Location: ../login.html?error=Error en el servidor');
        exit();
    }
} else {
    header('Location: ../login.html');
    exit();
}
?>
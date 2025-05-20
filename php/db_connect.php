<?php
$host = 'localhost';      // Tu servidor de base de datos
$dbname = 'centro_salud'; // Nombre de la base de datos
$username = 'root';       // Tu usuario de MySQL
$password = '';           // Tu contraseña de MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
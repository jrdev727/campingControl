<?php
// ============================================
// ARCHIVO DE CONFIGURACIÓN DE BASE DE DATOS
// ============================================
// Copia este archivo a conexion.php y modifica
// los valores según tu servidor de hosting
// ============================================

// Configuración LOCAL (desarrollo)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "camping_sonrisas";

// ============================================
// EJEMPLOS PARA DIFERENTES HOSTINGS:
// ============================================

// InfinityFree:
// $servername = "sql123.infinityfreeapp.com";
// $username = "epiz_xxxxx";
// $password = "tu_password";
// $dbname = "epiz_xxxxx_camping";

// 000webhost:
// $servername = "localhost";
// $username = "id12345_usuario";
// $password = "tu_password";
// $dbname = "id12345_camping";

// Hostinger:
// $servername = "localhost";
// $username = "u123456789_usuario";
// $password = "tu_password";
// $dbname = "u123456789_camping";

// ============================================
// NO MODIFICAR DEBAJO DE ESTA LÍNEA
// ============================================

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Si estamos esperando JSON, devolver error en JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'json') !== false) ||
        strpos($_SERVER['REQUEST_URI'], '.php') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "message" => "Error de conexión a la base de datos"
        ]);
        exit();
    } else {
        die("Error de conexión: " . $conn->connect_error);
    }
}

// Establecer charset UTF-8
$conn->set_charset("utf8mb4");
?>

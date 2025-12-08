<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "camping_sonrisas";

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
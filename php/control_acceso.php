<?php
require_once 'api_init.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
$conn->query("SET time_zone = '-03:00'");

// Obtener datos del formulario
$dni = $_POST['dni'] ?? '';
$tipo_entrada = $_POST['tipo_entrada'] ?? '';

// Definir precios según tipo de entrada
$precios = [
    'turista_adulto' => 8000,
    'turista_niño' => 4000,
    'turista_jubilado' => 4800,
    'local_adulto' => 4000,
    'local_niño' => 0,
    'local_jubilado' => 2400,
    'local' => 3000  // Compatibilidad con registros antiguos
];

// Validar que el tipo de entrada existe
if (!isset($precios[$tipo_entrada])) {
    echo json_encode(["success" => false, "message" => "Tipo de entrada inválido."]);
    exit();
}

$precio = $precios[$tipo_entrada];

// Obtener usuario_id de la sesión
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Insertar datos en la tabla de entradas usando prepared statement
$sql = "INSERT INTO entradas (tipo_entrada, precio, dni_cliente, usuario_id, estado)
        VALUES (?, ?, ?, ?, 'activo')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sdsi", $tipo_entrada, $precio, $dni, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Ingreso registrado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar el ingreso."]);
}

$stmt->close();
$conn->close();
?>
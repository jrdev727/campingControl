<?php
// Disable HTML error output and set JSON header
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'conexion.php';

// Obtener datos del formulario
$dni = $_POST['dni'] ?? '';
$tipo_entrada = $_POST['tipo_entrada'] ?? '';
$edad = $_POST['edad'] ?? 0;

// Definir precios según tipo de entrada
$precios = [
    'turista_adulto' => 8000,
    'turista_niño' => 5000,
    'local' => 3000
];

// Validar que el tipo de entrada existe
if (!isset($precios[$tipo_entrada])) {
    echo json_encode(["success" => false, "message" => "Tipo de entrada inválido."]);
    exit();
}

$precio = $precios[$tipo_entrada];

// Insertar datos en la tabla de entradas usando prepared statement
$sql = "INSERT INTO entradas (fecha_hora, tipo_entrada, precio, dni_cliente, edad)
        VALUES (NOW(), ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sdsi", $tipo_entrada, $precio, $dni, $edad);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Ingreso registrado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar el ingreso."]);
}

$stmt->close();
$conn->close();
?>
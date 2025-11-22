<?php
require_once 'conexion.php';

// Obtener datos del formulario
$dni = $_POST['dni'];
$tipo_entrada = $_POST['tipo_entrada'];
$edad = $_POST['edad'];

// Definir precios según tipo de entrada
$precios = [
    'turista_adulto' => 8000,
    'turista_niño' => 5000,
    'local' => 3000
];

$precio = $precios[$tipo_entrada];

// Insertar datos en la tabla de entradas
$sql = "INSERT INTO entradas (fecha_hora, tipo_entrada, precio, dni_cliente, edad)
        VALUES (NOW(), '$tipo_entrada', $precio, '$dni', $edad)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Ingreso registrado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar el ingreso."]);
}

$conn->close();
?>
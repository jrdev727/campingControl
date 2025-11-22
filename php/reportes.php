<?php
// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Consultar datos de la tabla entradas
$sql = "SELECT * FROM entradas ORDER BY fecha_hora DESC";
$resultado = $conn->query($sql);

$datos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $datos[] = $row;
    }
}

// Devolver datos en formato JSON
header('Content-Type: application/json');
echo json_encode($datos);

$conn->close();
?>
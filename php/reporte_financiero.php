<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
require_once 'conexion.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "No autenticado"]);
    exit();
}

try {
    $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

    // Ingresos por entradas
    $sql_entradas = "SELECT COALESCE(SUM(precio), 0) as total
                     FROM entradas
                     WHERE DATE(fecha_hora) BETWEEN ? AND ?
                     AND estado = 'activo'";

    $stmt = $conn->prepare($sql_entradas);
    $stmt->bind_param("ss", $fecha_desde, $fecha_hasta);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $ingresos_entradas = (float)$datos['total'];
    $stmt->close();

    // Ingresos por alquileres (actualmente 0 ya que no hay alquileres)
    $ingresos_alquileres = 0;

    // Total
    $ingresos_totales = $ingresos_entradas + $ingresos_alquileres;

    echo json_encode([
        'success' => true,
        'data' => [
            'ingresos_entradas' => $ingresos_entradas,
            'ingresos_alquileres' => $ingresos_alquileres,
            'ingresos_totales' => $ingresos_totales,
            'periodo' => [
                'desde' => $fecha_desde,
                'hasta' => $fecha_hasta
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al generar reporte: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

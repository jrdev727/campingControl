<?php
require_once 'api_init.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "No autenticado"]);
    exit();
}

try {
    $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

    // Total de entradas y sus ingresos
    $sql_entradas = "SELECT COUNT(*) as cantidad, COALESCE(SUM(precio), 0) as total
                     FROM entradas
                     WHERE DATE(fecha_hora) BETWEEN ? AND ?
                     AND estado = 'activo'";

    $stmt = $conn->prepare($sql_entradas);
    $stmt->bind_param("ss", $fecha_desde, $fecha_hasta);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $total_entradas = (int)$datos['cantidad'];
    $ingresos_totales = (float)$datos['total'];
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'total_entradas' => $total_entradas,
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

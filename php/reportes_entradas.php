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
    $tipo = $_GET['tipo'] ?? '';

    // Construir query base
    $sql = "SELECT tipo_entrada, COUNT(*) as cantidad, SUM(precio) as total, AVG(precio) as precio_unitario
            FROM entradas
            WHERE DATE(fecha_hora) BETWEEN ? AND ?
            AND estado = 'activo'";

    if ($tipo) {
        $sql .= " AND tipo_entrada = ?";
    }

    $sql .= " GROUP BY tipo_entrada";

    // Preparar statement
    if ($tipo) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $fecha_desde, $fecha_hasta, $tipo);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fecha_desde, $fecha_hasta);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    $por_tipo = [];
    $total_entradas = 0;
    $ingresos_totales = 0;

    while ($row = $resultado->fetch_assoc()) {
        $por_tipo[$row['tipo_entrada']] = [
            'cantidad' => (int)$row['cantidad'],
            'total' => (float)$row['total'],
            'precio_unitario' => (float)$row['precio_unitario']
        ];
        $total_entradas += (int)$row['cantidad'];
        $ingresos_totales += (float)$row['total'];
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'por_tipo' => $por_tipo,
            'total_entradas' => $total_entradas,
            'ingresos_totales' => $ingresos_totales
        ]
    ]);

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener reporte: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

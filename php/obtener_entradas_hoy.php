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
    // Obtener entradas del día actual
    $sql = "SELECT id, tipo_entrada, precio,
            DATE_FORMAT(fecha_hora, '%H:%i') as hora,
            DATE_FORMAT(fecha_hora, '%Y-%m-%d %H:%i:%s') as fecha_hora,
            estado
            FROM entradas
            WHERE DATE(fecha_hora) = CURDATE()
            ORDER BY fecha_hora DESC
            LIMIT 20";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $entradas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $entradas[] = $fila;
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => $entradas
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener entradas: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

<?php
require_once 'api_init.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "No autenticado"]);
    exit();
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit();
}

try {
    $entrada_id = $_POST['entrada_id'] ?? null;

    if (!$entrada_id) {
        echo json_encode(["success" => false, "error" => "ID de entrada requerido"]);
        exit();
    }

    // Verificar que la entrada existe y es del día actual
    $sql_verificar = "SELECT id, fecha_hora, estado
                      FROM entradas
                      WHERE id = ? AND DATE(fecha_hora) = CURDATE()";

    $stmt = $conn->prepare($sql_verificar);
    $stmt->bind_param("i", $entrada_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "error" => "Entrada no encontrada o no es del día actual"
        ]);
        $stmt->close();
        exit();
    }

    $entrada = $resultado->fetch_assoc();
    $stmt->close();

    // Verificar que no esté ya anulada
    if ($entrada['estado'] === 'anulado') {
        echo json_encode([
            "success" => false,
            "error" => "Esta entrada ya fue anulada"
        ]);
        exit();
    }

    // Anular la entrada (marcar como anulado en lugar de eliminar)
    $sql_anular = "UPDATE entradas SET estado = 'anulado' WHERE id = ?";
    $stmt = $conn->prepare($sql_anular);
    $stmt->bind_param("i", $entrada_id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Entrada anulada correctamente"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Error al anular la entrada"
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al anular entrada: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

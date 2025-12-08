<?php
header('Content-Type: application/json');
require_once 'conexion.php';

// Obtener datos del formulario
$tipo = $_POST['tipo'] ?? '';

if ($tipo === 'quincho') {
    // Datos específicos para reservar un quincho
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $personas = $_POST['personas'] ?? 0;

    // Verificar disponibilidad de quinchos
    $sql = "SELECT cantidad_disponible FROM inventario WHERE tipo_recurso = 'Quincho'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $disponibles = $row['cantidad_disponible'];

        if ($disponibles > 0) {
            // Actualizar inventario (reducir cantidad disponible)
            $nuevos_disponibles = $disponibles - 1;
            $update_sql = "UPDATE inventario SET cantidad_disponible = ? WHERE tipo_recurso = 'Quincho'";

            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $nuevos_disponibles);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Quincho reservado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el inventario."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "No hay quinchos disponibles."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al verificar la disponibilidad de quinchos."]);
    }
} elseif ($tipo === 'reposera') {
    // Datos específicos para alquilar una reposera
    $sql = "SELECT cantidad_disponible FROM inventario WHERE tipo_recurso = 'Reposera'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $disponibles = $row['cantidad_disponible'];

        if ($disponibles > 0) {
            // Actualizar inventario (reducir cantidad disponible)
            $nuevos_disponibles = $disponibles - 1;
            $update_sql = "UPDATE inventario SET cantidad_disponible = ? WHERE tipo_recurso = 'Reposera'";

            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $nuevos_disponibles);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Reposera alquilada correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el inventario."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "No hay reposeras disponibles."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al verificar la disponibilidad de reposeras."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Tipo de alquiler inválido."]);
}

$conn->close();
?>

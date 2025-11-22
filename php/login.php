<?php
session_start();
require_once 'conexion.php';

// Obtener datos del formulario
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Consultar usuario en la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario_db = $resultado->fetch_assoc();

    // Verificar contraseña
    if (password_verify($contraseña, $usuario_db['contraseña'])) {
        // Iniciar sesión
        $_SESSION['usuario_id'] = $usuario_db['id'];
        $_SESSION['rol'] = $usuario_db['rol'];

        // Redirigir según el rol
        if ($usuario_db['rol'] === 'administrador') {
            echo json_encode(["success" => true, "redirect" => "admin.php"]);
        } else {
            echo json_encode(["success" => true, "redirect" => "index.php"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
}

$conn->close();
?>
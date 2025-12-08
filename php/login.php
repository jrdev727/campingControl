<?php
// Disable HTML error output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set JSON header before any output
header('Content-Type: application/json');

session_start();
require_once 'conexion.php';

// Validate POST data
if (!isset($_POST['usuario']) || !isset($_POST['contraseña'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    exit();
}

// Obtener datos del formulario
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Consultar usuario en la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en la base de datos."]);
    $conn->close();
    exit();
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario_db = $resultado->fetch_assoc();

    // Verificar contraseña (soporta tanto hashed como texto plano)
    $password_valida = false;

    // Intentar primero con password_verify (para contraseñas hasheadas)
    if (password_verify($contraseña, $usuario_db['contraseña'])) {
        $password_valida = true;
    }
    // Si falla, comparar directamente (para contraseñas en texto plano)
    elseif ($contraseña === $usuario_db['contraseña']) {
        $password_valida = true;
    }

    if ($password_valida) {
        // Iniciar sesión
        $_SESSION['usuario_id'] = $usuario_db['id'];
        $_SESSION['usuario'] = $usuario_db['usuario'];
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

$stmt->close();
$conn->close();
?>
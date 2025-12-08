<?php
require_once 'php/conexion.php';

// Actualizar contraseñas en texto plano
$usuarios = [
    ['usuario' => 'admin', 'contraseña' => 'admin123', 'rol' => 'administrador'],
    ['usuario' => 'encargado', 'contraseña' => 'encargado123', 'rol' => 'encargado']
];

echo "<h2>Actualizando contraseñas...</h2>";

foreach ($usuarios as $user) {
    // Verificar si el usuario existe
    $check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $user['usuario']);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Actualizar contraseña
        $update = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE usuario = ?");
        $update->bind_param("ss", $user['contraseña'], $user['usuario']);
        if ($update->execute()) {
            echo "✓ Usuario '{$user['usuario']}' actualizado - Contraseña: {$user['contraseña']}<br>";
        } else {
            echo "✗ Error al actualizar '{$user['usuario']}'<br>";
        }
        $update->close();
    } else {
        // Insertar usuario nuevo
        $insert = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $user['usuario'], $user['contraseña'], $user['rol']);
        if ($insert->execute()) {
            echo "✓ Usuario '{$user['usuario']}' creado - Contraseña: {$user['contraseña']}<br>";
        } else {
            echo "✗ Error al crear '{$user['usuario']}'<br>";
        }
        $insert->close();
    }
    $check->close();
}

echo "<hr>";
echo "<h3>Credenciales de acceso:</h3>";
echo "<strong>Admin:</strong> admin / admin123<br>";
echo "<strong>Encargado:</strong> encargado / encargado123<br>";

$conn->close();
?>

<?php
require_once 'php/conexion.php';

echo "<h2>Diagnóstico de Usuarios</h2>";

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
echo "✓ Conexión a la base de datos exitosa<br><br>";

// Mostrar todos los usuarios
$sql = "SELECT id, usuario, contraseña, rol FROM usuarios";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Contraseña (primeros 50 chars)</th><th>Rol</th></tr>";

    while($row = $result->fetch_assoc()) {
        $password_preview = substr($row['contraseña'], 0, 50);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['usuario'] . "</td>";
        echo "<td>" . htmlspecialchars($password_preview) . "...</td>";
        echo "<td>" . $row['rol'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "⚠ No hay usuarios en la base de datos<br>";
    echo "Error: " . $conn->error;
}

// Probar login con credenciales conocidas
echo "<hr><h3>Prueba de Login</h3>";

$test_user = 'admin';
$test_pass = 'admin123';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $test_user);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario_db = $resultado->fetch_assoc();
    echo "Usuario encontrado: " . $usuario_db['usuario'] . "<br>";
    echo "Rol: " . $usuario_db['rol'] . "<br>";

    // Probar password_verify
    $verify_result = password_verify($test_pass, $usuario_db['contraseña']);
    echo "password_verify('admin123', hash): " . ($verify_result ? "✓ CORRECTO" : "✗ FALSO") . "<br>";

    // Probar comparación directa
    $direct_result = ($test_pass === $usuario_db['contraseña']);
    echo "Comparación directa ('admin123' === contraseña): " . ($direct_result ? "✓ CORRECTO" : "✗ FALSO") . "<br>";

    if ($verify_result || $direct_result) {
        echo "<br><strong style='color:green'>✓ Las credenciales admin/admin123 DEBERÍAN FUNCIONAR</strong>";
    } else {
        echo "<br><strong style='color:red'>✗ Las credenciales admin/admin123 NO FUNCIONAN</strong>";
        echo "<br>Contraseña en BD: '" . htmlspecialchars($usuario_db['contraseña']) . "'";
    }
} else {
    echo "✗ Usuario 'admin' no encontrado";
}

$conn->close();
?>

<?php
require_once 'php/conexion.php';

echo "<h2>Estructura de la tabla 'usuarios'</h2>";

// Mostrar columnas de la tabla
$sql = "DESCRIBE usuarios";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr><h3>Datos actuales:</h3>";

    // Mostrar todos los datos
    $sql2 = "SELECT * FROM usuarios";
    $result2 = $conn->query($sql2);

    if ($result2 && $result2->num_rows > 0) {
        echo "<pre>";
        while($row = $result2->fetch_assoc()) {
            print_r($row);
            echo "\n---\n";
        }
        echo "</pre>";
    } else {
        echo "No hay registros";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>

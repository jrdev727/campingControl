<?php
// Contraseñas en texto plano
$contraseña_admin = "admin123";
$contraseña_encargado = "encargado123";

// Cifrar contraseñas con password_hash()
echo "Contraseña para admin: " . password_hash($contraseña_admin, PASSWORD_DEFAULT) . "\n";
echo "Contraseña para encargado: " . password_hash($contraseña_encargado, PASSWORD_DEFAULT);
?>
<?php
// php/api_init.php
require_once 'cors.php';
require_once 'jwt_helper.php';

// Disable HTML error output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Check JWT
$token = JWT::getBearerToken();
$user_info = false;
if ($token) {
    $user_info = JWT::decode($token);
}

if (!$user_info) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No autorizado o token expirado"]);
    exit();
}

// Simulamos la sesión antigua para no tener que reescribir todo el backend
$_SESSION['usuario_id'] = $user_info['usuario_id'];
$_SESSION['usuario'] = $user_info['usuario'];
$_SESSION['rol'] = $user_info['rol'];

require_once 'conexion.php';
?>

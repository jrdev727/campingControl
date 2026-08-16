<?php
// php/cors.php

// Permitir solicitudes desde cualquier origen (Para desarrollo/Vercel)
// TODO: En producción, cambia '*' por 'https://tudominio.vercel.app'
header("Access-Control-Allow-Origin: https://camping-control-9kdh.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Manejar preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>

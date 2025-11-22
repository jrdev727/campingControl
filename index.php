<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Acceso - Camping Sonrisas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Camping Sonrisas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Control de Acceso</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="alquileres.php">Alquileres</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <div id="alerta" class="alert d-none" role="alert"></div>
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">Control de Acceso</h3>
            </div>
            <div class="card-body">
                <form id="formulario-ingreso">
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese su DNI" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo-entrada" class="form-label">Tipo de Entrada</label>
                        <select class="form-select" id="tipo-entrada" name="tipo_entrada" required>
                            <option value="turista_adulto">Turista (Adulto)</option>
                            <option value="turista_niño">Turista (Niño)</option>
                            <option value="local">Local</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edad" class="form-label">Edad</label>
                        <input type="number" class="form-control" id="edad" name="edad" placeholder="Ingrese su edad" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar Ingreso</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
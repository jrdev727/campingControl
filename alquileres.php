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
    <title>Gestión de Alquileres - Camping Sonrisas</title>
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
                        <a class="nav-link" href="index.php">Control de Acceso</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="alquileres.php">Alquileres</a>
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
        <h2 class="text-center mb-4">Gestión de Alquileres</h2>

        <!-- Reservar Quincho -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <h5 class="mb-0">Reservar Quincho</h5>
                    </div>
                    <div class="card-body">
                        <form id="reserva-quincho">
                            <input type="hidden" name="tipo" value="quincho">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora</label>
                                <input type="time" class="form-control" id="hora" name="hora" required>
                            </div>
                            <div class="mb-3">
                                <label for="personas" class="form-label">Cantidad de Personas</label>
                                <input type="number" class="form-control" id="personas" name="personas" min="1" max="10" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reservar Quincho</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Alquilar Reposera -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white text-center">
                        <h5 class="mb-0">Alquilar Reposera</h5>
                    </div>
                    <div class="card-body">
                        <form id="alquiler-reposera">
                            <input type="hidden" name="tipo" value="reposera">
                            <p class="text-muted">Seleccione para alquilar una reposera disponible.</p>
                            <button type="submit" class="btn btn-success w-100">Alquilar Reposera</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Variables para el layout
$paginaActual = 'control';
$tituloPagina = 'Punto de Venta';
$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador');
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
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none" role="alert"></div>

            <!-- Card de formulario -->
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Registrar Nueva Entrada</h3>
                            <p class="text-gray-500 mb-0 mt-2" style="font-size: 14px;">
                                Complete los datos del visitante para registrar su entrada al camping
                            </p>
                        </div>
                        <div class="card-body">
                            <form id="formulario-ingreso">
                                <div class="mb-4">
                                    <label for="dni" class="form-label">DNI del Visitante</label>
                                    <input type="text" class="form-control" id="dni" name="dni"
                                           placeholder="Ej: 12345678" required>
                                    <small class="text-gray-500">Ingrese el número de DNI sin puntos ni espacios</small>
                                </div>

                                <div class="mb-4">
                                    <label for="tipo-entrada" class="form-label">Tipo de Entrada</label>
                                    <select class="form-select" id="tipo-entrada" name="tipo_entrada" required>
                                        <option value="turista_adulto">Turista (Adulto) - $8.000</option>
                                        <option value="turista_niño">Turista (Niño) - $5.000</option>
                                        <option value="local">Local - $3.000</option>
                                    </select>
                                    <small class="text-gray-500">Seleccione el tipo de entrada según corresponda</small>
                                </div>

                                <div class="mb-4">
                                    <label for="edad" class="form-label">Edad del Visitante</label>
                                    <input type="number" class="form-control" id="edad" name="edad"
                                           placeholder="Ej: 25" min="1" max="120" required>
                                    <small class="text-gray-500">Ingrese la edad del visitante</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Registrar Entrada
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Tarifas de Entrada</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--brand-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            Turista Adulto
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: var(--brand-600);">
                                            $8.000
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--success-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            Turista Niño
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: var(--success-600);">
                                            $5.000
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--warning-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            Local
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: var(--warning-600);">
                                            $3.000
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>

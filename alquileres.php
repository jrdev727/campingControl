<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Variables para el layout
$paginaActual = 'alquileres';
$tituloPagina = 'Gestión de Alquileres';
$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador');
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
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none" role="alert"></div>

            <!-- Descripción -->
            <div class="mb-4">
                <p class="text-gray-500">
                    Gestione las reservas de quinchos y el alquiler de reposeras para los visitantes del camping.
                </p>
            </div>

            <!-- Cards de alquileres -->
            <div class="row g-4">
                <!-- Reservar Quincho -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background-color: var(--brand-50); border-bottom: 1px solid var(--brand-100);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; background-color: var(--brand-500); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="card-title mb-0">Reservar Quincho</h3>
                                    <p class="text-gray-500 mb-0 mt-1" style="font-size: 14px;">
                                        Reserve un quincho para su evento
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="reserva-quincho">
                                <input type="hidden" name="tipo" value="quincho">

                                <div class="mb-4">
                                    <label for="fecha" class="form-label">Fecha de Reserva</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                    <small class="text-gray-500">Seleccione la fecha para la reserva</small>
                                </div>

                                <div class="mb-4">
                                    <label for="hora" class="form-label">Hora de Inicio</label>
                                    <input type="time" class="form-control" id="hora" name="hora" required>
                                    <small class="text-gray-500">Horario de inicio del evento</small>
                                </div>

                                <div class="mb-4">
                                    <label for="personas" class="form-label">Cantidad de Personas</label>
                                    <input type="number" class="form-control" id="personas" name="personas"
                                           min="1" max="10" placeholder="Ej: 6" required>
                                    <small class="text-gray-500">Máximo 10 personas por quincho</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Confirmar Reserva
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Alquilar Reposera -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background-color: var(--success-50); border-bottom: 1px solid var(--success-100);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; background-color: var(--success-500); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="card-title mb-0">Alquilar Reposera</h3>
                                    <p class="text-gray-500 mb-0 mt-1" style="font-size: 14px;">
                                        Alquile una reposera disponible
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="alquiler-reposera">
                                <input type="hidden" name="tipo" value="reposera">

                                <!-- Información -->
                                <div class="mb-4 p-3" style="background-color: var(--gray-50); border-radius: 12px;">
                                    <div class="d-flex align-items-start gap-2">
                                        <svg width="20" height="20" fill="none" stroke="var(--gray-500)" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div style="font-size: 14px; color: var(--gray-600);">
                                            <strong>Información:</strong><br>
                                            Las reposeras están disponibles por orden de llegada.
                                            Al confirmar el alquiler, se asignará automáticamente una reposera disponible.
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas visuales -->
                                <div class="mb-4 p-4 text-center" style="background: linear-gradient(135deg, var(--success-50) 0%, var(--success-100) 100%); border-radius: 12px;">
                                    <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 8px;">
                                        Reposeras Disponibles
                                    </div>
                                    <div style="font-size: 36px; font-weight: 700; color: var(--success-600);" id="reposeras-disponibles">
                                        -
                                    </div>
                                    <div style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
                                        Consulte disponibilidad en tiempo real
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Alquilar Reposera
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Precios y Disponibilidad -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información de Servicios</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <h5 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                                        📋 Quinchos
                                    </h5>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                                            <span style="color: var(--gray-600);">Capacidad:</span>
                                            <strong style="float: right; color: var(--gray-900);">Hasta 10 personas</strong>
                                        </li>
                                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                                            <span style="color: var(--gray-600);">Incluye:</span>
                                            <strong style="float: right; color: var(--gray-900);">Parrilla y mesas</strong>
                                        </li>
                                        <li style="padding: 8px 0;">
                                            <span style="color: var(--gray-600);">Reserva:</span>
                                            <strong style="float: right; color: var(--gray-900);">Con anticipación</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h5 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                                        🏖️ Reposeras
                                    </h5>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                                            <span style="color: var(--gray-600);">Disponibilidad:</span>
                                            <strong style="float: right; color: var(--gray-900);">Por orden de llegada</strong>
                                        </li>
                                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                                            <span style="color: var(--gray-600);">Duración:</span>
                                            <strong style="float: right; color: var(--gray-900);">Todo el día</strong>
                                        </li>
                                        <li style="padding: 8px 0;">
                                            <span style="color: var(--gray-600);">Asignación:</span>
                                            <strong style="float: right; color: var(--gray-900);">Automática</strong>
                                        </li>
                                    </ul>
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

    <script>
        // Cargar disponibilidad de reposeras al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarDisponibilidad();
            // Actualizar cada 30 segundos
            setInterval(cargarDisponibilidad, 30000);
        });

        function cargarDisponibilidad() {
            fetch('php/dashboard_stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data.inventario) {
                        const reposeras = result.data.inventario.Reposera || 0;
                        document.getElementById('reposeras-disponibles').textContent = reposeras;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar disponibilidad:', error);
                });
        }
    </script>
</body>
</html>

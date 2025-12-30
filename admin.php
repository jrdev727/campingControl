<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// Variables para el layout
$paginaActual = 'admin';
$tituloPagina = 'Dashboard Principal';
$esAdmin = true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Camping Sonrisas</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Header con botón de actualización -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-gray-900 fw-bold mb-1">Resumen General</h2>
                    <p class="text-gray-500 mb-0">Vista rápida del estado del camping</p>
                </div>
                <button id="btn-refresh" class="btn btn-outline-primary btn-icon-only shadow-sm" title="Actualizar datos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </button>
            </div>

            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none" role="alert"></div>

            <!-- Métricas principales -->
            <div class="row g-4 mb-4">
                <!-- Entradas Hoy -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Entradas Hoy</div>
                        <div class="metric-value-huge" id="entradas-hoy">0</div>
                        <div class="badge bg-success-subtle text-success-emphasis rounded-pill px-3">
                            +$<span id="ingresos-hoy">0</span>
                        </div>
                    </div>
                </div>

                <!-- Entradas del Mes -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle" style="color: #7f56d9; background: #f9f5ff;">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Total del Mes</div>
                        <div class="metric-value-huge" id="entradas-mes">0</div>
                        <span class="metric-change positive" id="cambio-mes">0%</span>
                    </div>
                </div>

                <!-- Comparación con Ayer -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle" style="color: #039855; background: #ecfdf3;">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Vs. Ayer</div>
                        <div class="metric-value-huge" id="comparacion-ayer">0</div>
                        <span class="metric-change" id="cambio-ayer">0%</span>
                    </div>
                </div>

                <!-- Clima Actual -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="metric-card-premium text-center d-flex flex-column justify-content-center p-3">
                        <div id="clima-widget">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Tablas -->
            <div class="row g-4">
                <!-- Gráfico de Ingresos -->
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 rounded-top-4">
                            <h3 class="card-title fw-bold text-gray-900">Ingresos Semanales</h3>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <canvas id="chartIngresos" height="320"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Distribución de Tipos de Entrada -->
                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 rounded-top-4">
                            <h3 class="card-title fw-bold text-gray-900">Horarios Pico (Hoy)</h3>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex flex-column justify-content-center">
                            <div style="position: relative; height: 260px;">
                                <canvas id="chartHoras"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Dashboard Logic -->
    <script src="js/dashboard.js"></script>
</body>
</html>

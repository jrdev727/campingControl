<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Variables para el layout
$paginaActual = 'reportes';
$tituloPagina = 'Reportes y Estadísticas';
$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Camping Sonrisas</title>
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
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Métricas superiores (Quick View) -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-md-4">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Visitantes Hoy</div>
                        <div class="metric-value-huge" id="visitantes-hoy">0</div>
                        <div class="text-gray-500 small">Personas registradas</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle" style="color: #039855; background: #ecfdf3;">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Ingresos Hoy</div>
                        <div class="metric-value-huge text-success-600" style="color: var(--success-600);">$<span id="ingresos-hoy-valor">0</span></div>
                        <div class="text-gray-500 small">Recaudación diaria</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card-premium">
                        <div class="metric-icon-circle" style="color: #7f56d9; background: #f9f5ff;">
                            <svg class="metric-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="metric-label-caps">Acumulado Mes</div>
                        <div class="metric-value-huge text-brand-600" style="color: var(--brand-600);">$<span id="ingresos-mes-valor">0</span></div>
                        <div class="text-gray-500 small"><span id="visitantes-mes">0</span> visitantes este mes</div>
                    </div>
                </div>
            </div>

            <!-- Navegación de Pestañas -->
            <ul class="nav nav-tabs-premium" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#reporte-entradas">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: text-bottom;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Reporte de Entradas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reporte-financiero">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: text-bottom;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reporte Financiero
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab: Reporte de Entradas -->
                <div class="tab-pane fade show active" id="reporte-entradas">
                    <!-- Filtros -->
                    <div class="report-filters">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-gray-700">Desde</label>
                                <input type="date" class="form-control" id="fecha-desde-entradas">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-gray-700">Hasta</label>
                                <input type="date" class="form-control" id="fecha-hasta-entradas">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-gray-700">Tipo</label>
                                <select class="form-select" id="filtro-tipo-entrada">
                                    <option value="">Todos los tipos</option>
                                    <option value="turista_adulto">No Residente (Adulto)</option>
                                    <option value="turista_niño">No Residente (Niño)</option>
                                    <option value="local">Residentes</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100 fw-bold" onclick="buscarEntradas()">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido Reporte -->
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                    <h3 class="card-title fw-bold text-gray-900">Distribución de Entradas</h3>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="exportarPDF()">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" class="me-1">
                                            <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Descargar PDF
                                    </button>
                                </div>
                                <div class="card-body px-4 pb-4">
                                    <div style="position: relative; height: 300px;">
                                        <canvas id="chartEntradasTipo"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="report-summary-card">
                                <h5 class="fw-bold text-gray-900 mb-4">Resumen del Período</h5>
                                
                                <div class="mb-4">
                                    <div class="text-uppercase text-gray-500" style="font-size: 12px; font-weight: 600; letter-spacing: 0.05em;">Total Visitantes</div>
                                    <div class="fs-2 fw-bold text-gray-900" id="total-entradas-resumen">0</div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-uppercase text-gray-500" style="font-size: 12px; font-weight: 600; letter-spacing: 0.05em;">Recaudación Total</div>
                                    <div class="fs-2 fw-bold text-success-600" style="color: var(--success-600);">$<span id="ingresos-totales-resumen">0</span></div>
                                </div>

                                <hr class="border-gray-200 my-4">

                                <div id="breakdown-tipos" class="breakdown-list">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Reporte Financiero -->
                <div class="tab-pane fade" id="reporte-financiero">
                    <div class="report-filters">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-gray-700">Desde</label>
                                <input type="date" class="form-control" id="fecha-desde-financiero">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-gray-700">Hasta</label>
                                <input type="date" class="form-control" id="fecha-hasta-financiero">
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary w-100 fw-bold" onclick="generarReporteFinanciero()">
                                        Generar
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="exportarPDFFinanciero()" id="btn-exportar-financiero" style="display: none;">
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="metric-card-premium bg-brand-gradient text-white" style="background: linear-gradient(135deg, var(--brand-600) 0%, var(--brand-800) 100%); border: none;">
                                <div class="metric-label-caps text-white opacity-75">Total Entradas</div>
                                <div class="metric-value-huge text-white" id="total-entradas-fin">0</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="metric-card-premium bg-success-gradient text-white" style="background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%); border: none;">
                                <div class="metric-label-caps text-white opacity-75">Ingresos Totales</div>
                                <div class="metric-value-huge text-white">$<span id="ingresos-totales-fin">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Div oculto para impresión -->
    <div id="reporte-print" style="display: none;"></div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reportes.js?v=<?php echo time(); ?>"></script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Variables para el layout
$paginaActual = 'reportes';
$tituloPagina = 'Dashboard & Reportes';
$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Camping Sonrisas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .report-tabs {
            background: var(--gray-800);
            border-radius: 12px 12px 0 0;
            padding: 0;
            margin-bottom: 0;
        }
        .report-tabs .nav-link {
            color: var(--gray-400);
            border: none;
            padding: 16px 24px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .report-tabs .nav-link.active {
            background: var(--gray-700);
            color: white;
            border-radius: 12px 12px 0 0;
        }
        .filters-section {
            background: var(--success-600);
            color: white;
            padding: 24px;
            border-radius: 0 0 12px 12px;
            margin-bottom: 24px;
        }
        .filters-section label {
            color: white;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .summary-card {
            background: var(--gray-800);
            padding: 20px;
            border-radius: 12px;
            color: white;
        }
        .summary-value {
            font-size: 28px;
            font-weight: 700;
            margin: 8px 0;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-700);
        }
        .breakdown-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Métricas superiores -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Visitantes Hoy</div>
                        <div class="metric-value" id="visitantes-hoy">0</div>
                        <div class="text-gray-500" style="font-size: 14px;">Total de ingresos del día</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Ingresos Hoy</div>
                        <div class="metric-value" style="color: var(--success-600);">$<span id="ingresos-hoy-valor">0</span></div>
                        <div class="text-gray-500" style="font-size: 14px;">Entradas + Alquileres</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Alquileres Activos</div>
                        <div class="metric-value" style="color: var(--warning-600);">0</div>
                        <div class="text-gray-500" style="font-size: 14px;">En uso o reservados</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Ingresos del Mes</div>
                        <div class="metric-value" style="color: var(--brand-600);">$<span id="ingresos-mes-valor">0</span></div>
                        <div class="text-gray-500" style="font-size: 14px;"><span id="visitantes-mes">0</span> visitantes</div>
                    </div>
                </div>
            </div>

            <!-- Tabs de reportes -->
            <ul class="nav nav-tabs report-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#reporte-entradas">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: middle;">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Reporte de Entradas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reporte-financiero">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: middle;">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reporte Financiero
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab: Reporte de Entradas -->
                <div class="tab-pane fade show active" id="reporte-entradas">
                    <!-- Filtros -->
                    <div class="filters-section">
                        <h5 style="margin-bottom: 20px;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: middle;">
                                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtros
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label>Desde</label>
                                <input type="date" class="form-control" id="fecha-desde-entradas" value="">
                            </div>
                            <div class="col-md-4">
                                <label>Hasta</label>
                                <input type="date" class="form-control" id="fecha-hasta-entradas" value="">
                            </div>
                            <div class="col-md-3">
                                <label>Tipo de Entrada</label>
                                <select class="form-select" id="filtro-tipo-entrada">
                                    <option value="">Todos</option>
                                    <option value="turista_adulto">No Residente (Adulto)</option>
                                    <option value="turista_niño">No Residente (Niño)</option>
                                    <option value="local">Residente</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-light w-100" onclick="buscarEntradas()">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos y resumen -->
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">Entradas por Tipo</h3>
                                    <button class="btn btn-sm btn-primary" onclick="exportarPDF()">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;">
                                            <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Exportar PDF
                                    </button>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartEntradasTipo" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <h5 style="margin-bottom: 20px;">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Resumen
                                </h5>
                                <div style="margin-bottom: 24px;">
                                    <div style="font-size: 14px; color: var(--gray-400);">Total de Entradas</div>
                                    <div class="summary-value" id="total-entradas-resumen">0</div>
                                </div>
                                <div>
                                    <div style="font-size: 14px; color: var(--gray-400);">Ingresos Totales</div>
                                    <div class="summary-value" style="color: var(--success-500);">$<span id="ingresos-totales-resumen">0</span></div>
                                </div>
                                <hr style="border-color: var(--gray-700); margin: 24px 0;">
                                <div id="breakdown-tipos"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Reporte Financiero -->
                <div class="tab-pane fade" id="reporte-financiero">
                    <div class="filters-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 style="margin: 0;">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: middle;">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Período
                            </h5>
                            <button class="btn btn-light" onclick="exportarPDFFinanciero()" id="btn-exportar-financiero" style="display: none;">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;">
                                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Exportar PDF
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label>Desde</label>
                                <input type="date" class="form-control" id="fecha-desde-financiero">
                            </div>
                            <div class="col-md-5">
                                <label>Hasta</label>
                                <input type="date" class="form-control" id="fecha-hasta-financiero">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-light w-100" onclick="generarReporteFinanciero()">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="metric-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="metric-label" style="color: white;">Ingresos por Entradas</div>
                                <div class="metric-value" style="color: white;">$<span id="ingresos-entradas-fin">0</span></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="metric-label" style="color: white;">Ingresos por Alquileres</div>
                                <div class="metric-value" style="color: white;">$<span id="ingresos-alquileres-fin">0</span></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="metric-label" style="color: white;">Ingresos Totales</div>
                                <div class="metric-value" style="color: white;">$<span id="ingresos-totales-fin">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reportes.js"></script>
</body>
</html>

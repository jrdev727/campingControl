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
    <title>Punto de Venta - Camping Sonrisas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }

            body > *:not(#ticket-print) {
                display: none !important;
            }

            #ticket-print {
                display: block !important;
                position: static !important;
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            #ticket-print * {
                visibility: visible !important;
            }
        }
        .entrada-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: var(--gray-50);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .entrada-item-info {
            flex: 1;
        }
        .entrada-item-actions button {
            padding: 4px 12px;
            font-size: 12px;
        }
        .total-section {
            background: var(--brand-50);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none" role="alert"></div>

            <div class="row">
                <!-- Columna izquierda: Formulario de entrada -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Agregar Entradas</h3>
                            <p class="text-gray-500 mb-0 mt-2" style="font-size: 14px;">
                                Seleccione el tipo de entrada y la cantidad
                            </p>
                        </div>
                        <div class="card-body">
                            <form id="formulario-agregar">
                                <div class="mb-4">
                                    <label for="tipo-entrada" class="form-label">Tipo de Entrada</label>
                                    <select class="form-select" id="tipo-entrada" name="tipo_entrada" required>
                                        <option value="turista_adulto">No Residente (Adulto) - $8.000</option>
                                        <option value="turista_niño">No Residente (Niño) - $5.000</option>
                                        <option value="local">Residente - $3.000</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad"
                                           value="1" min="1" max="100" required>
                                    <small class="text-gray-500">¿Cuántas entradas de este tipo?</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tarifas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Tarifas de Entrada</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--brand-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            No Residente Adulto
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: var(--brand-600);">
                                            $8.000
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--success-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            No Residente Niño
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: var(--success-600);">
                                            $5.000
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-center p-3" style="background-color: var(--warning-50); border-radius: 12px;">
                                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 4px;">
                                            Residente
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

                <!-- Columna derecha: Carrito y resumen -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">Carrito de Entradas</h3>
                                <p class="text-gray-500 mb-0 mt-1" style="font-size: 14px;">
                                    <span id="total-items">0</span> entrada(s) en el carrito
                                </p>
                            </div>
                            <button id="btn-limpiar" class="btn btn-sm btn-outline-danger" style="display: none;">
                                Limpiar
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="carrito-lista" class="mb-3">
                                <p class="text-center text-gray-500 py-5">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto; display: block; opacity: 0.3;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    El carrito está vacío
                                </p>
                            </div>

                            <div class="total-section" id="total-section" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div style="font-size: 16px; font-weight: 600; color: var(--gray-700);">
                                        Total a Cobrar:
                                    </div>
                                    <div id="total-precio" style="font-size: 32px; font-weight: 700; color: var(--brand-600);">
                                        $0
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button id="btn-registrar" class="btn btn-success btn-lg">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Registrar Venta
                                    </button>
                                    <button id="btn-imprimir" class="btn btn-outline-primary" style="display: none;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Imprimir Ticket
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas entradas del día -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Últimas Entradas de Hoy</h3>
                            <button class="btn btn-sm btn-outline-primary" onclick="cargarUltimasEntradas()">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Actualizar
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Hora</th>
                                            <th>Tipo</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-ultimas-entradas">
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500">
                                                Cargando...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Ticket oculto para impresión -->
    <div id="ticket-print" style="display: none;">
        <div style="font-family: 'Courier New', monospace; width: 80mm; padding: 5mm; background: #fff; color: #000;">
            <!-- Tickets -->
            <div id="ticket-contenido"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pos.js"></script>
</body>
</html>

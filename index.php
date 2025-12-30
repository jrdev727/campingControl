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
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            body { margin: 0; padding: 0; }
            body > *:not(#ticket-print) { display: none !important; }
            #ticket-print {
                display: block !important;
                position: static !important;
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            #ticket-print * { visibility: visible !important; }
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="main-content">
            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none shadow-sm border-0" role="alert"></div>

            <div class="row g-4">
                <!-- Columna izquierda: Formulario de entrada -->
                <div class="col-12 col-xl-7">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 rounded-top-4">
                            <h3 class="card-title fw-bold text-gray-900">Nueva Venta</h3>
                            <p class="text-gray-500 mb-0 mt-1 small">
                                Seleccione tipo y cantidad
                            </p>
                        </div>
                        <div class="card-body px-4 pt-4 pb-4">
                            <form id="formulario-agregar">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label for="tipo-entrada" class="form-label fw-semibold text-gray-700">Tipo de Entrada</label>
                                        <select class="form-select form-select-lg" id="tipo-entrada" name="tipo_entrada" required>
                                            <option value="turista_adulto">No Residente (Adulto) - $8.000</option>
                                            <option value="turista_niño">No Residente (Niño) - $4.000</option>
                                            <option value="turista_jubilado">No Residente (Jubilado) - $4.800</option>
                                            <option value="local_adulto">Residente (Adulto) - $4.000</option>
                                            <option value="local_niño">Residente (Niño) - GRATIS</option>
                                            <option value="local_jubilado">Residente (Jubilado) - $2.400</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cantidad" class="form-label fw-semibold text-gray-700">Cantidad</label>
                                        <input type="number" class="form-control form-control-lg text-center" id="cantidad" name="cantidad" value="1" min="1" max="100" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-4">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Agregar al Carrito
                                </button>
                                
                                <hr class="text-gray-200">
                                
                                <h5 class="fw-bold text-gray-900 mb-3 fs-6 px-1">Referencia de Tarifas</h5>
                                
                                <!-- Accordion de Tarifas -->
                                <div class="accordion accordion-flush" id="accordionTarifas">
                                    <div class="accordion-item border-0 mb-2 shadow-sm rounded-3 overflow-hidden">
                                        <h2 class="accordion-header" id="headingTurista">
                                            <button class="accordion-button collapsed fw-semibold text-brand-700 bg-brand-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTurista">
                                                No Residentes (Turistas)
                                            </button>
                                        </h2>
                                        <div id="collapseTurista" class="accordion-collapse collapse" data-bs-parent="#accordionTarifas">
                                            <div class="accordion-body p-2 bg-white">
                                                <div class="list-group list-group-flush">
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('turista_adulto')">
                                                        <span>Adulto</span>
                                                        <span class="badge bg-brand-100 text-brand-700 rounded-pill">$8.000</span>
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('turista_niño')">
                                                        <span>Niño</span>
                                                        <span class="badge bg-success-100 text-success-700 rounded-pill">$4.000</span>
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('turista_jubilado')">
                                                        <span>Jubilado</span>
                                                        <span class="badge bg-purple-100 text-purple-700 rounded-pill">$4.800</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="accordion-item border-0 mb-2 shadow-sm rounded-3 overflow-hidden">
                                        <h2 class="accordion-header" id="headingLocal">
                                            <button class="accordion-button collapsed fw-semibold text-warning-700 bg-warning-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLocal">
                                                Residentes (Locales)
                                            </button>
                                        </h2>
                                        <div id="collapseLocal" class="accordion-collapse collapse" data-bs-parent="#accordionTarifas">
                                            <div class="accordion-body p-2 bg-white">
                                                <div class="list-group list-group-flush">
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('local_adulto')">
                                                        <span>Adulto</span>
                                                        <span class="badge bg-warning-100 text-warning-700 rounded-pill">$4.000</span>
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('local_niño')">
                                                        <span>Niño</span>
                                                        <span class="badge bg-green-100 text-green-700 rounded-pill">GRATIS</span>
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarTarifa('local_jubilado')">
                                                        <span>Jubilado</span>
                                                        <span class="badge bg-orange-100 text-orange-700 rounded-pill">$2.400</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Carrito y resumen -->
                <div class="col-12 col-xl-5">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 rounded-top-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title fw-bold text-gray-900 mb-0">Carrito</h3>
                                <p class="text-gray-500 mb-0 mt-1 small">
                                    <span id="total-items">0</span> items
                                </p>
                            </div>
                            <button id="btn-limpiar" class="btn btn-sm btn-light text-danger fw-medium" style="display: none;">
                                Vaciar
                            </button>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4 d-flex flex-column">
                            <div id="carrito-lista" class="flex-grow-1 overflow-auto" style="min-height: 200px; max-height: 400px;">
                                <div class="text-center text-gray-400 py-5">
                                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mb-0">Tu carrito está vacío</p>
                                </div>
                            </div>

                            <div class="pos-total-section" id="total-section" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="text-gray-600 fw-medium">Total a Pagar</div>
                                    <div id="total-precio" class="fs-2 fw-bold text-brand-600" style="color: var(--brand-600);">$0</div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button id="btn-registrar" class="btn btn-success btn-lg py-3 rounded-3 fw-bold shadow-sm">
                                        Confirmar Venta
                                    </button>
                                    <button id="btn-imprimir" class="btn btn-outline-primary btn-lg py-3 rounded-3 fw-bold" style="display: none;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-2" style="display: inline-block; vertical-align: text-bottom;">
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
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center rounded-top-4">
                            <h3 class="card-title fw-bold text-gray-900 mb-0">Últimas Ventas</h3>
                            <button class="btn btn-sm btn-icon-only btn-light text-primary" onclick="cargarUltimasEntradas()" title="Actualizar">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-uppercase text-gray-500 small fw-bold">Hora</th>
                                            <th class="px-4 py-3 text-uppercase text-gray-500 small fw-bold">Tipo</th>
                                            <th class="px-4 py-3 text-uppercase text-gray-500 small fw-bold">Precio</th>
                                            <th class="px-4 py-3 text-uppercase text-gray-500 small fw-bold">Estado</th>
                                            <th class="px-4 py-3 text-uppercase text-gray-500 small fw-bold">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-ultimas-entradas" class="border-top-0">
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500 py-4">
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

    <!-- Modal de confirmación para anular entrada -->
    <div class="modal fade" id="modalAnularEntrada" tabindex="-1" aria-labelledby="modalAnularEntradaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold" id="modalAnularEntradaLabel">
                        Confirmar Anulación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger opacity-75">
                         <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p class="mb-2 fw-medium fs-5">¿Está seguro de anular esta entrada?</p>
                    <p class="text-muted mb-0 small">
                        Esta acción marcará la entrada como anulada y no se podrá revertir.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-gray-50 p-3 justify-content-center">
                    <button type="button" class="btn btn-white border shadow-sm px-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-4" id="btn-confirmar-anular">
                        Sí, Anular
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pos.js"></script>
    <script>
        // Helper inline para selección de tarifa rápida
        function seleccionarTarifa(tipo) {
            document.getElementById('tipo-entrada').value = tipo;
        }
    </script>
</body>
</html>

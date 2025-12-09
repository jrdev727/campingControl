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
            <!-- Contenedor de alertas -->
            <div id="alerta" class="alert d-none" role="alert"></div>

            <!-- Métricas principales -->
            <div class="row g-4 mb-4">
                <!-- Entradas Hoy -->
                <div class="col-12 col-md-6">
                    <div class="metric-card">
                        <div class="metric-icon-wrapper">
                            <svg class="metric-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label">Entradas Hoy</div>
                        <div class="metric-value" id="entradas-hoy">0</div>
                        <div class="text-gray-500" style="font-size: 14px;">
                            Ingresos: $<span id="ingresos-hoy">0</span>
                        </div>
                    </div>
                </div>

                <!-- Entradas del Mes -->
                <div class="col-12 col-md-6">
                    <div class="metric-card">
                        <div class="metric-icon-wrapper">
                            <svg class="metric-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="metric-label">Total del Mes</div>
                        <div class="metric-value" id="entradas-mes">0</div>
                        <span class="metric-change positive" id="cambio-mes">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            0%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Tablas -->
            <div class="row g-4">
                <!-- Gráfico de Ingresos -->
                <div class="col-12 col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ingresos de la Última Semana</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartIngresos" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Distribución de Tipos de Entrada -->
                <div class="col-12 col-xl-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tipos de Entrada Hoy</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartTipos" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Adicionales -->
            <div class="row g-4 mt-2">
                <!-- Ticket Promedio -->
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon-wrapper">
                            <svg class="metric-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label">Ticket Promedio Hoy</div>
                        <div class="metric-value">$<span id="ticket-promedio">0</span></div>
                    </div>
                </div>

                <!-- Hora Pico -->
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon-wrapper">
                            <svg class="metric-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="metric-label">Hora Pico Hoy</div>
                        <div class="metric-value" id="hora-pico">--</div>
                    </div>
                </div>

                <!-- Comparación con Ayer -->
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon-wrapper">
                            <svg class="metric-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="metric-label">Entradas vs Ayer</div>
                        <div class="metric-value" id="comparacion-ayer">0</div>
                        <span class="metric-change" id="cambio-ayer">0%</span>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Entradas por Hora y Clima -->
            <div class="row g-4 mt-2">
                <!-- Gráfico de Entradas por Hora -->
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Entradas por Hora - Hoy</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartHoras" height="280"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Widget del Clima -->
                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Clima Actual</h3>
                        </div>
                        <div class="card-body text-center" id="clima-widget">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let chartIngresos = null;
        let chartTipos = null;
        let chartHoras = null;

        // Cargar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
            cargarClima();
            // Actualizar cada 30 segundos
            setInterval(cargarEstadisticas, 30000);
            // Actualizar clima cada 10 minutos
            setInterval(cargarClima, 600000);
        });

        function cargarEstadisticas() {
            fetch('php/dashboard_stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        actualizarDashboard(result.data);
                    } else {
                        mostrarAlerta('Error al cargar estadísticas', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('Error de conexión', 'danger');
                });
        }

        function actualizarDashboard(data) {
            // Actualizar métricas principales
            document.getElementById('entradas-hoy').textContent = data.entradas_hoy.total;
            document.getElementById('ingresos-hoy').textContent = formatearPrecio(data.entradas_hoy.ingresos);

            document.getElementById('entradas-mes').textContent = data.entradas_mes.total;

            // Actualizar cambio porcentual del mes
            const cambioMes = document.getElementById('cambio-mes');
            const porcentaje = data.entradas_mes.cambio_porcentaje || 0;

            if (porcentaje >= 0) {
                cambioMes.className = 'metric-change positive';
                cambioMes.innerHTML = `
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    ${Math.abs(porcentaje).toFixed(1)}%
                `;
            } else {
                cambioMes.className = 'metric-change negative';
                cambioMes.innerHTML = `
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    ${Math.abs(porcentaje).toFixed(1)}%
                `;
            }

            // Actualizar estadísticas adicionales
            document.getElementById('ticket-promedio').textContent = formatearPrecio(data.promedio_ticket || 0);

            // Hora pico
            const horaPico = data.hora_pico;
            if (horaPico !== null && horaPico !== undefined) {
                document.getElementById('hora-pico').textContent = `${horaPico}:00`;
            } else {
                document.getElementById('hora-pico').textContent = '--';
            }

            // Comparación con ayer
            const hoy = data.entradas_hoy.total;
            const ayer = data.ayer.total;
            document.getElementById('comparacion-ayer').textContent = hoy;

            const cambioAyer = document.getElementById('cambio-ayer');
            if (ayer > 0) {
                const porcentajeAyer = ((hoy - ayer) / ayer) * 100;
                if (porcentajeAyer >= 0) {
                    cambioAyer.className = 'metric-change positive';
                    cambioAyer.innerHTML = `
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        +${Math.abs(porcentajeAyer).toFixed(1)}%
                    `;
                } else {
                    cambioAyer.className = 'metric-change negative';
                    cambioAyer.innerHTML = `
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        ${Math.abs(porcentajeAyer).toFixed(1)}%
                    `;
                }
            } else {
                cambioAyer.className = 'metric-change';
                cambioAyer.textContent = hoy > 0 ? '+100%' : '0%';
            }

            // Actualizar gráficos
            actualizarGraficoIngresos(data.ingresos_semana);
            actualizarGraficoTipos(data.tipos_entrada);
            actualizarGraficoHoras(data.entradas_por_hora);
        }

        function actualizarGraficoIngresos(datos) {
            const ctx = document.getElementById('chartIngresos').getContext('2d');

            const labels = datos.map(d => {
                const fecha = new Date(d.fecha);
                return fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            });
            const values = datos.map(d => d.ingresos);

            if (chartIngresos) {
                chartIngresos.destroy();
            }

            chartIngresos = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: values,
                        borderColor: '#465fff',
                        backgroundColor: 'rgba(70, 95, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString('es-AR');
                                }
                            }
                        }
                    }
                }
            });
        }

        function actualizarGraficoTipos(tipos) {
            const ctx = document.getElementById('chartTipos').getContext('2d');

            const labels = Object.keys(tipos);
            const values = Object.values(tipos);

            if (chartTipos) {
                chartTipos.destroy();
            }

            chartTipos = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#465fff',
                            '#12b76a',
                            '#f79009'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function actualizarGraficoHoras(datos) {
            const ctx = document.getElementById('chartHoras').getContext('2d');

            // Crear array de 24 horas con valores
            const horas = Array.from({length: 24}, (_, i) => i);
            const valores = horas.map(h => datos[h] || 0);
            const labels = horas.map(h => `${h}:00`);

            if (chartHoras) {
                chartHoras.destroy();
            }

            chartHoras = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Entradas',
                        data: valores,
                        backgroundColor: '#465fff',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        function cargarClima() {
            // Using wttr.in free weather service (no API key needed)
            // Location: General Belgrano, Buenos Aires, Argentina
            fetch('https://wttr.in/General_Belgrano,Buenos_Aires?format=j1')
                .then(response => response.json())
                .then(data => {
                    const current = data.current_condition[0];
                    const temp = current.temp_C;
                    const desc = current.lang_es ? current.lang_es[0].value : current.weatherDesc[0].value;
                    const humidity = current.humidity;
                    const windKmph = current.windspeedKmph;

                    // Get weather icon based on weather code
                    const weatherCode = current.weatherCode;
                    let iconClass = '☀️'; // Default sunny

                    if (weatherCode === '113') iconClass = '☀️'; // Sunny
                    else if (['116', '119', '122'].includes(weatherCode)) iconClass = '⛅'; // Partly cloudy
                    else if (['143', '248', '260'].includes(weatherCode)) iconClass = '🌫️'; // Fog
                    else if (['176', '263', '266', '293', '296'].includes(weatherCode)) iconClass = '🌦️'; // Light rain
                    else if (['299', '302', '305', '308', '356', '359'].includes(weatherCode)) iconClass = '🌧️'; // Rain
                    else if (['200', '386', '389', '392', '395'].includes(weatherCode)) iconClass = '⛈️'; // Thunderstorm
                    else if (['227', '230', '323', '326', '329', '332', '335', '338', '368', '371', '374', '377'].includes(weatherCode)) iconClass = '🌨️'; // Snow

                    document.getElementById('clima-widget').innerHTML = `
                        <div style="font-size: 48px; margin-bottom: 10px;">${iconClass}</div>
                        <h2 class="mb-2">${temp}°C</h2>
                        <p class="text-muted mb-2">${desc}</p>
                        <div class="d-flex justify-content-center gap-3">
                            <small>💧 ${humidity}%</small>
                            <small>💨 ${windKmph} km/h</small>
                        </div>
                        <small class="text-muted mt-2 d-block">General Belgrano, BA</small>
                    `;
                })
                .catch(error => {
                    console.error('Error al cargar clima:', error);
                    document.getElementById('clima-widget').innerHTML = `
                        <p class="text-muted">No se pudo cargar el clima</p>
                        <small>Intente nuevamente más tarde</small>
                    `;
                });
        }

        function formatearPrecio(precio) {
            if (precio === undefined || precio === null || isNaN(precio)) {
                return '0';
            }
            return precio.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        function mostrarAlerta(mensaje, tipo) {
            const alerta = document.getElementById('alerta');
            alerta.className = `alert alert-${tipo}`;
            alerta.textContent = mensaje;
            alerta.classList.remove('d-none');

            setTimeout(() => {
                alerta.classList.add('d-none');
            }, 5000);
        }
    </script>
</body>
</html>

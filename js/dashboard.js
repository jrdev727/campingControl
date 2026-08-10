/**
 * Dashboard Logic - Camping Sonrisas
 * Handles charts, weather updates, and statistics.
 */

let chartIngresos = null;
let chartTipos = null;

document.addEventListener('DOMContentLoaded', function () {
    cargarEstadisticas();
    cargarClima();

    // Auto-update intervals
    setInterval(cargarEstadisticas, 30000); // 30s
    setInterval(cargarClima, 600000); // 10m

    // Manual Refresh Button Handler
    document.getElementById('btn-refresh')?.addEventListener('click', function () {
        const icon = this.querySelector('svg');
        icon.classList.add('spin-anim'); // Add animation class
        this.disabled = true;

        Promise.all([cargarEstadisticas(), cargarClima()]).then(() => {
            setTimeout(() => {
                icon.classList.remove('spin-anim');
                this.disabled = false;
            }, 500);
        });
    });
});

function cargarEstadisticas() {
    return window.fetchWithAuth(`${API_BASE_URL}/dashboard_stats.php`)
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
    // Actualizar métricas principales con animación de conteo (opcional, por ahora directo)
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
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#465fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 13 },
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 11 },
                        callback: function (value) {
                            return '$' + value.toLocaleString('es-AR');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11 }
                    }
                }
            }
        }
    });
}

function actualizarGraficoHoras(horasData) {
    const ctx = document.getElementById('chartHoras').getContext('2d');

    // Preparar datos (0 a 23 horas)
    const labels = [];
    const values = [];
    const backgroundColors = [];

    // Generar etiquetas y valores para todas las horas (o solo las relevantes si se prefiere)
    // Aquí mostramos rango de 8:00 a 22:00 que es lo típico, o todas si hay datos fuera de rango
    for (let i = 0; i < 24; i++) {
        // Solo mostrar si es un horario operativo razonable o tiene datos
        if (i >= 8 && i <= 22) {
            labels.push(i + ':00');
            const val = horasData[i] || 0;
            values.push(val);

            // Highlight peak hours
            if (val > 0 && val === Math.max(...Object.values(horasData))) {
                backgroundColors.push('#f79009'); // Orange for peak
            } else {
                backgroundColors.push('#465fff'); // Brand for normal
            }
        }
    }

    if (chartTipos) {
        chartTipos.destroy();
    }

    chartTipos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Entradas',
                data: values,
                backgroundColor: backgroundColors,
                borderRadius: 4,
                hoverBackgroundColor: '#2a31d8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return 'Hora: ' + context[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function cargarClima() {
    return fetch('https://wttr.in/El_Pingo,Entre_Rios?format=j1')
        .then(response => response.json())
        .then(data => {
            const current = data.current_condition[0];
            const temp = current.temp_C;
            const desc = current.lang_es ? current.lang_es[0].value : current.weatherDesc[0].value;
            const humidity = current.humidity;

            // Get weather icon based on weather code
            const weatherCode = current.weatherCode;
            let iconClass = '☀️';

            if (weatherCode === '113') iconClass = '☀️';
            else if (['116', '119', '122'].includes(weatherCode)) iconClass = '⛅';
            else if (['143', '248', '260'].includes(weatherCode)) iconClass = '🌫️';
            else if (['176', '263', '266', '293', '296'].includes(weatherCode)) iconClass = '🌦️';
            else if (['299', '302', '305', '308', '356', '359'].includes(weatherCode)) iconClass = '🌧️';
            else if (['200', '386', '389', '392', '395'].includes(weatherCode)) iconClass = '⛈️';
            else if (['227', '230', '323', '326', '329', '332', '335', '338', '368', '371', '374', '377'].includes(weatherCode)) iconClass = '🌨️';

            const widget = document.getElementById('clima-widget');
            widget.innerHTML = `
                <div class="weather-icon-bounce" style="font-size: 42px; margin-bottom: 8px;">${iconClass}</div>
                <div class="metric-value mb-0" style="font-size: 32px;">${temp}°C</div>
                <div class="text-gray-500 fw-medium" style="font-size: 14px; text-transform: capitalize;">${desc}</div>
                <div class="badge bg-gray-100 text-gray-700 mt-2">💧 ${humidity}% Humedad</div>
            `;
        })
        .catch(error => {
            console.error('Error al cargar clima:', error);
            document.getElementById('clima-widget').innerHTML = `
                <div class="text-muted" style="font-size: 14px;">Clima no disponible</div>
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
    alerta.className = `alert alert-${tipo} shadow-sm border-0`;
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');
    alerta.classList.add('fade-in-down');

    setTimeout(() => {
        alerta.classList.add('d-none');
        alerta.classList.remove('fade-in-down');
    }, 5000);
}

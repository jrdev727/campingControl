// Variables globales
let chartEntradasTipo = null;
let datosActuales = null;

// Inicializar fechas al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const hoy = new Date().toISOString().split('T')[0];
    const primerDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];

    document.getElementById('fecha-desde-entradas').value = primerDiaMes;
    document.getElementById('fecha-hasta-entradas').value = hoy;
    document.getElementById('fecha-desde-financiero').value = primerDiaMes;
    document.getElementById('fecha-hasta-financiero').value = hoy;

    // Cargar métricas superiores
    cargarMetricasSuperiores();

    // Cargar datos iniciales
    buscarEntradas();
});

// Cargar métricas superiores
async function cargarMetricasSuperiores() {
    try {
        const response = await fetch('php/dashboard_stats.php');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            document.getElementById('visitantes-hoy').textContent = data.entradas_hoy.total;
            document.getElementById('ingresos-hoy-valor').textContent = formatearPrecio(data.entradas_hoy.ingresos);
            document.getElementById('ingresos-mes-valor').textContent = formatearPrecio(data.entradas_mes.ingresos);
            document.getElementById('visitantes-mes').textContent = data.entradas_mes.total;
        }
    } catch (error) {
        console.error('Error al cargar métricas:', error);
    }
}

// Buscar entradas con filtros
async function buscarEntradas() {
    const fechaDesde = document.getElementById('fecha-desde-entradas').value;
    const fechaHasta = document.getElementById('fecha-hasta-entradas').value;
    const tipoEntrada = document.getElementById('filtro-tipo-entrada').value;

    try {
        const params = new URLSearchParams({
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta,
            tipo: tipoEntrada
        });

        const response = await fetch(`php/reportes_entradas.php?${params}`);
        const result = await response.json();

        if (result.success) {
            datosActuales = result.data;
            actualizarReporteEntradas(result.data);
        }
    } catch (error) {
        console.error('Error al buscar entradas:', error);
    }
}

// Actualizar reporte de entradas
function actualizarReporteEntradas(data) {
    // Actualizar resumen
    document.getElementById('total-entradas-resumen').textContent = data.total_entradas;
    document.getElementById('ingresos-totales-resumen').textContent = formatearPrecio(data.ingresos_totales);

    // Actualizar breakdown por tipo
    const breakdown = document.getElementById('breakdown-tipos');
    let breakdownHTML = '';

    const nombresTipos = {
        'turista_adulto': 'No Residente Adulto',
        'turista_niño': 'No Residente Niño',
        'local': 'Residente'
    };

    for (const [tipo, datos] of Object.entries(data.por_tipo)) {
        const nombre = nombresTipos[tipo] || tipo;
        breakdownHTML += `
            <div class="breakdown-item">
                <div>
                    <div style="font-weight: 600;">${nombre}: ${datos.cantidad}</div>
                    <div style="font-size: 12px; color: var(--gray-400);">($${formatearPrecio(datos.precio_unitario)})</div>
                </div>
                <div style="font-weight: 700; color: var(--success-500);">$${formatearPrecio(datos.total)}</div>
            </div>
        `;
    }

    breakdown.innerHTML = breakdownHTML;

    // Actualizar gráfico
    actualizarGraficoEntradas(data.por_tipo);
}

// Actualizar gráfico de entradas
function actualizarGraficoEntradas(porTipo) {
    const ctx = document.getElementById('chartEntradasTipo').getContext('2d');

    const nombresTipos = {
        'turista_adulto': 'No Residente Adulto',
        'turista_niño': 'No Residente Niño',
        'local': 'Residente'
    };

    const labels = Object.keys(porTipo).map(tipo => nombresTipos[tipo] || tipo);
    const values = Object.values(porTipo).map(datos => datos.cantidad);
    const colores = ['#5A99D4', '#C9A961', '#5BACAD'];

    if (chartEntradasTipo) {
        chartEntradasTipo.destroy();
    }

    chartEntradasTipo = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de Entradas',
                data: values,
                backgroundColor: colores,
                borderWidth: 0
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
                        label: function(context) {
                            return context.parsed.y + ' entradas';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Generar reporte financiero
async function generarReporteFinanciero() {
    const fechaDesde = document.getElementById('fecha-desde-financiero').value;
    const fechaHasta = document.getElementById('fecha-hasta-financiero').value;

    try {
        const params = new URLSearchParams({
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        });

        const response = await fetch(`php/reporte_financiero.php?${params}`);
        const result = await response.json();

        if (result.success) {
            document.getElementById('ingresos-entradas-fin').textContent = formatearPrecio(result.data.ingresos_entradas);
            document.getElementById('ingresos-alquileres-fin').textContent = formatearPrecio(result.data.ingresos_alquileres);
            document.getElementById('ingresos-totales-fin').textContent = formatearPrecio(result.data.ingresos_totales);
        }
    } catch (error) {
        console.error('Error al generar reporte financiero:', error);
    }
}

// Exportar a PDF (simplificado)
function exportarPDF() {
    if (!datosActuales) {
        alert('No hay datos para exportar');
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título
    doc.setFontSize(18);
    doc.text('Reporte de Entradas', 20, 20);

    // Fecha
    doc.setFontSize(11);
    const fechaDesde = document.getElementById('fecha-desde-entradas').value;
    const fechaHasta = document.getElementById('fecha-hasta-entradas').value;
    doc.text(`Período: ${fechaDesde} al ${fechaHasta}`, 20, 30);

    // Resumen
    doc.setFontSize(14);
    doc.text('Resumen', 20, 45);
    doc.setFontSize(11);
    doc.text(`Total de Entradas: ${datosActuales.total_entradas}`, 20, 55);
    doc.text(`Ingresos Totales: $${formatearPrecio(datosActuales.ingresos_totales)}`, 20, 65);

    // Detalle por tipo
    doc.setFontSize(14);
    doc.text('Detalle por Tipo', 20, 80);
    doc.setFontSize(11);

    let y = 90;
    const nombresTipos = {
        'turista_adulto': 'No Residente Adulto',
        'turista_niño': 'No Residente Niño',
        'local': 'Residente'
    };

    for (const [tipo, datos] of Object.entries(datosActuales.por_tipo)) {
        const nombre = nombresTipos[tipo] || tipo;
        doc.text(`${nombre}: ${datos.cantidad} x $${formatearPrecio(datos.precio_unitario)} = $${formatearPrecio(datos.total)}`, 20, y);
        y += 10;
    }

    // Guardar
    doc.save(`reporte-entradas-${fechaDesde}-${fechaHasta}.pdf`);
}

// Formatear precio
function formatearPrecio(precio) {
    return precio.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

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

            // Mostrar botón de exportar PDF
            document.getElementById('btn-exportar-financiero').style.display = 'inline-block';
        }
    } catch (error) {
        console.error('Error al generar reporte financiero:', error);
    }
}

// Exportar reporte financiero a PDF
async function exportarPDFFinanciero() {
    const fechaDesde = document.getElementById('fecha-desde-financiero').value;
    const fechaHasta = document.getElementById('fecha-hasta-financiero').value;

    try {
        const params = new URLSearchParams({
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        });

        const response = await fetch(`php/reporte_financiero.php?${params}`);
        const result = await response.json();

        if (!result.success) {
            alert('Error al generar el reporte');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Título
        doc.setFontSize(20);
        doc.setFont('helvetica', 'bold');
        doc.text('Reporte Financiero', 105, 20, { align: 'center' });

        doc.setFontSize(14);
        doc.setFont('helvetica', 'normal');
        doc.text('Camping Sonrisas Compartidas', 105, 30, { align: 'center' });

        // Período
        doc.setFontSize(11);
        doc.setTextColor(100, 100, 100);
        doc.text(`Período: ${fechaDesde} - ${fechaHasta}`, 105, 40, { align: 'center' });

        let y = 60;

        // Cuadro de Ingresos por Entradas
        doc.setFillColor(102, 126, 234);
        doc.roundedRect(20, y, 170, 25, 3, 3, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Ingresos por Entradas', 30, y + 10);

        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text(`$${formatearPrecio(result.data.ingresos_entradas)}`, 30, y + 20);

        y += 35;

        // Cuadro de Ingresos por Alquileres
        doc.setFillColor(240, 147, 251);
        doc.roundedRect(20, y, 170, 25, 3, 3, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Ingresos por Alquileres', 30, y + 10);

        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text(`$${formatearPrecio(result.data.ingresos_alquileres)}`, 30, y + 20);

        y += 35;

        // Línea separadora
        doc.setDrawColor(200, 200, 200);
        doc.setLineWidth(0.5);
        doc.line(20, y, 190, y);

        y += 15;

        // Cuadro de Total
        doc.setFillColor(79, 172, 254);
        doc.roundedRect(20, y, 170, 30, 3, 3, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('TOTAL DE INGRESOS', 30, y + 12);

        doc.setFontSize(22);
        doc.text(`$${formatearPrecio(result.data.ingresos_totales)}`, 30, y + 24);

        // Pie de página
        doc.setFontSize(9);
        doc.setTextColor(150, 150, 150);
        doc.setFont('helvetica', 'normal');
        const fecha = new Date().toLocaleDateString('es-AR');
        doc.text(`Generado el ${fecha}`, 105, 280, { align: 'center' });

        // Guardar
        doc.save(`reporte-financiero-${fechaDesde}-${fechaHasta}.pdf`);

    } catch (error) {
        console.error('Error al exportar PDF financiero:', error);
        alert('Error al generar el PDF');
    }
}

// Exportar a PDF con formato de tabla
async function exportarPDF() {
    const fechaDesde = document.getElementById('fecha-desde-entradas').value;
    const fechaHasta = document.getElementById('fecha-hasta-entradas').value;

    try {
        // Obtener datos detallados por fecha
        const params = new URLSearchParams({
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        });

        const response = await fetch(`php/reporte_pdf.php?${params}`);
        const result = await response.json();

        if (!result.success) {
            alert('Error al generar el reporte');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Título
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text('Reporte Financiero - Camping Sonrisas', 105, 20, { align: 'center' });

        // Resumen superior
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        let y = 35;

        doc.text(`Ingresos por Entradas: $${formatearPrecio(result.data.totales.ingresos_entradas)}`, 20, y);
        y += 10;
        doc.setFont('helvetica', 'bold');
        doc.text(`Total: $${formatearPrecio(result.data.totales.total)}`, 20, y);

        y += 15;

        // Tabla - Encabezado
        doc.setFillColor(41, 128, 185); // Azul
        doc.rect(20, y, 170, 10, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.text('Fecha', 30, y + 7);
        doc.text('Entradas', 90, y + 7);
        doc.text('Total', 150, y + 7);

        y += 10;

        // Tabla - Filas
        doc.setTextColor(0, 0, 0);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let isAlternate = false;
        result.data.por_fecha.forEach((fila) => {
            // Alternar color de fondo
            if (isAlternate) {
                doc.setFillColor(240, 240, 240);
                doc.rect(20, y, 170, 8, 'F');
            }

            doc.text(fila.fecha, 30, y + 6);
            doc.text(`$${formatearPrecio(fila.ingresos_entradas)}`, 90, y + 6);
            doc.text(`$${formatearPrecio(fila.total)}`, 150, y + 6);

            y += 8;
            isAlternate = !isAlternate;

            // Nueva página si es necesario
            if (y > 270) {
                doc.addPage();
                y = 20;
                isAlternate = false;
            }
        });

        // Guardar
        doc.save(`reporte-financiero-${fechaDesde}-${fechaHasta}.pdf`);

    } catch (error) {
        console.error('Error al exportar PDF:', error);
        alert('Error al generar el PDF');
    }
}

// Formatear precio
function formatearPrecio(precio) {
    return precio.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

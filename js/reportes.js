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
        'turista_adulto': 'No Residente (Adulto)',
        'turista_niño': 'No Residente (Niño)',
        'local': 'Residente',
        // Variaciones antiguas
        'no_residente_adulto': 'No Residente (Adulto)',
        'No Residente Adulto': 'No Residente (Adulto)',
        'no_residente_niño': 'No Residente (Niño)',
        'No Residente Niño': 'No Residente (Niño)',
        'residente': 'Residente',
        'Residente': 'Residente'
    };

    // Agrupar por nombre normalizado
    const porTipoNormalizado = {};
    for (const [tipo, datos] of Object.entries(data.por_tipo)) {
        const nombreNormalizado = nombresTipos[tipo] || tipo;
        if (!porTipoNormalizado[nombreNormalizado]) {
            porTipoNormalizado[nombreNormalizado] = {
                cantidad: 0,
                total: 0,
                precio_unitario: datos.precio_unitario
            };
        }
        porTipoNormalizado[nombreNormalizado].cantidad += datos.cantidad;
        porTipoNormalizado[nombreNormalizado].total += datos.total;
    }

    for (const [nombre, datos] of Object.entries(porTipoNormalizado)) {
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
        'turista_adulto': 'No Residente (Adulto)',
        'turista_niño': 'No Residente (Niño)',
        'local': 'Residente',
        // Variaciones antiguas
        'no_residente_adulto': 'No Residente (Adulto)',
        'No Residente Adulto': 'No Residente (Adulto)',
        'no_residente_niño': 'No Residente (Niño)',
        'No Residente Niño': 'No Residente (Niño)',
        'residente': 'Residente',
        'Residente': 'Residente'
    };

    // Agrupar por nombre normalizado
    const porTipoNormalizado = {};
    for (const [tipo, datos] of Object.entries(porTipo)) {
        const nombreNormalizado = nombresTipos[tipo] || tipo;
        if (!porTipoNormalizado[nombreNormalizado]) {
            porTipoNormalizado[nombreNormalizado] = { cantidad: 0 };
        }
        porTipoNormalizado[nombreNormalizado].cantidad += datos.cantidad;
    }

    const labels = Object.keys(porTipoNormalizado);
    const values = Object.values(porTipoNormalizado).map(datos => datos.cantidad);
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
            document.getElementById('total-entradas-fin').textContent = result.data.total_entradas || 0;
            document.getElementById('ingresos-totales-fin').textContent = formatearPrecio(result.data.ingresos_totales);

            // Mostrar botón de exportar
            const btnPDF = document.getElementById('btn-exportar-financiero');
            if (btnPDF) {
                btnPDF.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error al generar reporte financiero:', error);
        alert('Error al generar el reporte: ' + error.message);
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
        // Formato para ticketera (48mm para compensar márgenes de hardware de la impresora)
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: [48, 150] // 48mm de ancho x 150mm de alto
        });

        const centerX = 24; // Centro del documento (48/2)
        let y = 5;

        // Separador superior
        doc.setDrawColor(0);
        doc.setLineWidth(0.5);
        doc.line(0, y, 48, y);
        y += 5;

        // Título
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text('REPORTE FINANCIERO', centerX, y, { align: 'center' });
        y += 5;

        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text('Camping Sonrisas', centerX, y, { align: 'center' });
        y += 4;
        doc.text('Compartidas', centerX, y, { align: 'center' });
        y += 6;

        // Separador
        doc.line(0, y, 48, y);
        y += 5;

        // Período
        doc.setFontSize(7);
        doc.text('PERIODO:', centerX, y, { align: 'center' });
        y += 4;
        doc.setFont('helvetica', 'bold');
        doc.text(`${fechaDesde}`, centerX, y, { align: 'center' });
        y += 3.5;
        doc.text('a', centerX, y, { align: 'center' });
        y += 3.5;
        doc.text(`${fechaHasta}`, centerX, y, { align: 'center' });
        y += 6;

        // Separador
        doc.setFont('helvetica', 'normal');
        doc.line(0, y, 48, y);
        y += 5;

        // Total de Entradas
        doc.setFontSize(8);
        doc.text('Total de Entradas:', centerX, y, { align: 'center' });
        y += 5;
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(`${result.data.total_entradas || 0}`, centerX, y, { align: 'center' });
        y += 8;

        // Separador
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7);
        doc.line(0, y, 48, y);
        y += 5;

        // Ingresos Totales
        doc.setFontSize(8);
        doc.text('Ingresos Totales:', centerX, y, { align: 'center' });
        y += 5;
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(`$${formatearPrecio(result.data.ingresos_totales)}`, centerX, y, { align: 'center' });
        y += 8;

        // Separador
        doc.setFont('helvetica', 'normal');
        doc.line(0, y, 48, y);
        y += 5;

        // Pie de página
        doc.setFontSize(6);
        const fecha = new Date().toLocaleDateString('es-AR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        doc.text(`Generado: ${fecha}`, centerX, y, { align: 'center' });
        y += 4;
        doc.line(0, y, 48, y);

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
        // Calcular altura necesaria según cantidad de registros
        const alturaBase = 80;
        const alturaPorFila = 8;
        const alturaTotal = alturaBase + (result.data.por_fecha.length * alturaPorFila);

        // Formato para ticketera (48mm para compensar márgenes de hardware)
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: [48, Math.max(150, alturaTotal)]
        });

        const centerX = 24;
        let y = 5;

        // Separador superior
        doc.setDrawColor(0);
        doc.setLineWidth(0.5);
        doc.line(0, y, 48, y);
        y += 5;

        // Título
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.text('REPORTE DE ENTRADAS', centerX, y, { align: 'center' });
        y += 5;

        doc.setFontSize(7);
        doc.setFont('helvetica', 'normal');
        doc.text('Camping Sonrisas', centerX, y, { align: 'center' });
        y += 6;

        // Separador
        doc.line(0, y, 48, y);
        y += 5;

        // Período
        doc.setFontSize(6);
        doc.text('PERIODO:', centerX, y, { align: 'center' });
        y += 3.5;
        doc.setFont('helvetica', 'bold');
        doc.text(`${fechaDesde} a ${fechaHasta}`, centerX, y, { align: 'center' });
        y += 5;

        // Separador
        doc.setFont('helvetica', 'normal');
        doc.line(0, y, 48, y);
        y += 4;

        // Resumen
        doc.setFontSize(7);
        doc.text('Total Ingresos:', 1, y);
        doc.setFont('helvetica', 'bold');
        doc.text(`$${formatearPrecio(result.data.totales.total)}`, 47, y, { align: 'right' });
        y += 5;

        // Separador
        doc.setFont('helvetica', 'normal');
        doc.line(0, y, 48, y);
        y += 4;

        // Encabezado tabla
        doc.setFontSize(6);
        doc.setFont('helvetica', 'bold');
        doc.text('FECHA', 1, y);
        doc.text('TOTAL', 47, y, { align: 'right' });
        y += 3;

        doc.setLineWidth(0.3);
        doc.line(0, y, 48, y);
        y += 3;

        // Filas de la tabla
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(6);

        result.data.por_fecha.forEach((fila, index) => {
            // Fecha (formato corto)
            const fechaCorta = fila.fecha.split('-').reverse().join('/');
            doc.text(fechaCorta, 1, y);

            // Total
            doc.text(`$${formatearPrecio(fila.total)}`, 47, y, { align: 'right' });

            y += 4;

            // Línea separadora cada 3 filas
            if ((index + 1) % 3 === 0 && index < result.data.por_fecha.length - 1) {
                doc.setLineWidth(0.1);
                doc.setDrawColor(200);
                doc.line(0, y - 1, 48, y - 1);
                doc.setDrawColor(0);
            }
        });

        // Separador final
        doc.setLineWidth(0.5);
        doc.line(0, y, 48, y);
        y += 4;

        // Pie de página
        doc.setFontSize(5);
        const fecha = new Date().toLocaleDateString('es-AR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        doc.text(`Generado: ${fecha}`, centerX, y, { align: 'center' });
        y += 3;
        doc.line(0, y, 48, y);

        // Guardar
        doc.save(`reporte-entradas-${fechaDesde}-${fechaHasta}.pdf`);

    } catch (error) {
        console.error('Error al exportar PDF:', error);
        alert('Error al generar el PDF');
    }
}

// Formatear precio
function formatearPrecio(precio) {
    if (precio === undefined || precio === null || isNaN(precio)) {
        return '0';
    }
    return precio.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// ============================================
// FUNCIONES DE IMPRESIÓN DIRECTA (58mm)
// ============================================

// Imprimir reporte financiero en ticketera
async function imprimirReporteFinanciero() {
    const fechaDesde = document.getElementById('fecha-desde-financiero').value;
    const fechaHasta = document.getElementById('fecha-hasta-financiero').value;

    const totalEntradas = document.getElementById('total-entradas-fin').textContent;
    const ingresosTotales = document.getElementById('ingresos-totales-fin').textContent;

    const fecha = new Date().toLocaleDateString('es-AR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const reporteDiv = document.getElementById('reporte-print');
    reporteDiv.innerHTML = `
        <div class="separador"></div>
        <div class="titulo">REPORTE FINANCIERO</div>
        <div class="subtitulo">Camping Sonrisas Compartidas</div>
        <div class="separador"></div>
        <div class="campo" style="text-align: center;">PERIODO:</div>
        <div class="campo" style="text-align: center; font-weight: bold;">${fechaDesde}</div>
        <div class="campo" style="text-align: center; font-weight: bold;">a</div>
        <div class="campo" style="text-align: center; font-weight: bold;">${fechaHasta}</div>
        <div class="separador"></div>
        <div class="campo" style="text-align: center;">Total de Entradas:</div>
        <div class="valor-grande">${totalEntradas}</div>
        <div class="separador"></div>
        <div class="campo" style="text-align: center;">Ingresos Totales:</div>
        <div class="valor-grande">$${ingresosTotales}</div>
        <div class="separador"></div>
        <div class="pie">Generado: ${fecha}</div>
        <div class="separador"></div>
    `;

    reporteDiv.style.display = 'block';
    setTimeout(() => {
        window.print();
        reporteDiv.style.display = 'none';
    }, 100);
}

// Imprimir reporte de entradas en ticketera
async function imprimirReporteEntradas() {
    const fechaDesde = document.getElementById('fecha-desde-entradas').value;
    const fechaHasta = document.getElementById('fecha-hasta-entradas').value;

    try {
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

        const fecha = new Date().toLocaleDateString('es-AR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        let contenidoRegistros = '';
        result.data.por_fecha.forEach((fila, index) => {
            const fechaCorta = fila.fecha.split('-').reverse().join('/');
            contenidoRegistros += `
                <div class="registro-item">
                    <div class="campo-vertical">FECHA:</div>
                    <div class="valor-vertical">${fechaCorta}</div>
                    <div class="campo-vertical">ENTRADAS:</div>
                    <div class="valor-vertical">${fila.cantidad_entradas}</div>
                    <div class="campo-vertical">TOTAL:</div>
                    <div class="valor-vertical">$${formatearPrecio(fila.total)}</div>
                </div>
                ${index < result.data.por_fecha.length - 1 ? '<div class="separador"></div>' : ''}
            `;
        });

        const reporteDiv = document.getElementById('reporte-print');
        reporteDiv.innerHTML = `
            <div class="separador"></div>
            <div class="titulo">REPORTE DE ENTRADAS</div>
            <div class="subtitulo">Camping Sonrisas</div>
            <div class="separador"></div>
            <div class="campo-vertical" style="text-align: center;">PERIODO:</div>
            <div class="valor-vertical" style="text-align: center;">${fechaDesde}</div>
            <div class="valor-vertical" style="text-align: center;">a</div>
            <div class="valor-vertical" style="text-align: center;">${fechaHasta}</div>
            <div class="separador"></div>
            <div class="campo-vertical">Total Ingresos:</div>
            <div class="valor-vertical">$${formatearPrecio(result.data.totales.total)}</div>
            <div class="separador"></div>
            ${contenidoRegistros}
            <div class="separador"></div>
            <div class="pie">Generado: ${fecha}</div>
            <div class="separador"></div>
        `;

        reporteDiv.style.display = 'block';
        setTimeout(() => {
            window.print();
            reporteDiv.style.display = 'none';
        }, 100);

    } catch (error) {
        console.error('Error al imprimir reporte:', error);
        alert('Error al generar el reporte para impresión');
    }
}

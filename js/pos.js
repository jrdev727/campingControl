// Sistema de Punto de Venta
let carrito = [];
let entradaIdAAnular = null; // ID de entrada a anular

// Cargar últimas entradas al iniciar y configurar modal
document.addEventListener('DOMContentLoaded', function() {
    cargarUltimasEntradas();

    // Configurar botón de confirmación del modal
    const btnConfirmarAnular = document.getElementById('btn-confirmar-anular');
    if (btnConfirmarAnular) {
        btnConfirmarAnular.addEventListener('click', function() {
            if (entradaIdAAnular !== null) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAnularEntrada'));
                modal.hide();

                // Anular la entrada
                anularEntrada(entradaIdAAnular);
                entradaIdAAnular = null;
            }
        });
    }
});

const precios = {
    'turista_adulto': 8000,
    'turista_niño': 4000,
    'turista_jubilado': 4800,
    'local_adulto': 4000,
    'local_niño': 0,
    'local_jubilado': 2400,
    // Compatibilidad con registros antiguos
    'local': 3000
};

const nombres = {
    'turista_adulto': 'No Residente (Adulto)',
    'turista_niño': 'No Residente (Niño)',
    'turista_jubilado': 'No Residente (Jubilado)',
    'local_adulto': 'Residente (Adulto)',
    'local_niño': 'Residente (Niño)',
    'local_jubilado': 'Residente (Jubilado)',
    // Compatibilidad con registros antiguos
    'local': 'Residente'
};

// Agregar al carrito
document.getElementById('formulario-agregar')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const tipoEntrada = document.getElementById('tipo-entrada').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);

    // Buscar si ya existe en el carrito
    const existente = carrito.find(item => item.tipo === tipoEntrada);

    if (existente) {
        existente.cantidad += cantidad;
    } else {
        carrito.push({
            tipo: tipoEntrada,
            nombre: nombres[tipoEntrada],
            precio: precios[tipoEntrada],
            cantidad: cantidad
        });
    }

    actualizarCarrito();

    // Reset cantidad
    document.getElementById('cantidad').value = 1;
});

// Actualizar visualización del carrito
function actualizarCarrito() {
    const carritoLista = document.getElementById('carrito-lista');
    const totalItems = document.getElementById('total-items');
    const totalPrecio = document.getElementById('total-precio');
    const totalSection = document.getElementById('total-section');
    const btnLimpiar = document.getElementById('btn-limpiar');

    if (carrito.length === 0) {
        carritoLista.innerHTML = `
            <p class="text-center text-gray-500 py-5">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto; display: block; opacity: 0.3;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                El carrito está vacío
            </p>
        `;
        totalItems.textContent = '0';
        totalSection.style.display = 'none';
        btnLimpiar.style.display = 'none';
        return;
    }

    let html = '';
    let totalCantidad = 0;
    let totalDinero = 0;

    carrito.forEach((item, index) => {
        const subtotal = item.precio * item.cantidad;
        totalCantidad += item.cantidad;
        totalDinero += subtotal;

        html += `
            <div class="entrada-item">
                <div class="entrada-item-info">
                    <div style="font-weight: 600; color: var(--gray-900);">${item.nombre}</div>
                    <div style="font-size: 14px; color: var(--gray-600);">
                        ${item.cantidad} x $${item.precio.toLocaleString('es-AR')} = $${subtotal.toLocaleString('es-AR')}
                    </div>
                </div>
                <div class="entrada-item-actions">
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
    });

    carritoLista.innerHTML = html;
    totalItems.textContent = totalCantidad;
    totalPrecio.textContent = '$' + totalDinero.toLocaleString('es-AR');
    totalSection.style.display = 'block';
    btnLimpiar.style.display = 'inline-block';
}

// Eliminar del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

// Limpiar carrito
document.getElementById('btn-limpiar')?.addEventListener('click', function() {
    if (confirm('¿Está seguro de limpiar el carrito?')) {
        carrito = [];
        actualizarCarrito();
        document.getElementById('btn-imprimir').style.display = 'none';
    }
});

// Registrar venta
document.getElementById('btn-registrar')?.addEventListener('click', async function() {
    if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = 'Procesando...';

    try {
        // Registrar cada entrada en la base de datos
        const promesas = [];

        for (const item of carrito) {
            for (let i = 0; i < item.cantidad; i++) {
                const formData = new FormData();
                formData.append('tipo_entrada', item.tipo);
                formData.append('dni', ''); // Sin DNI
                formData.append('edad', 0); // Edad por defecto

                promesas.push(
                    fetch('php/control_acceso.php', {
                        method: 'POST',
                        body: formData
                    }).then(r => r.json())
                );
            }
        }

        const resultados = await Promise.all(promesas);

        // Verificar si todos fueron exitosos
        const todosExitosos = resultados.every(r => r.success);

        if (todosExitosos) {
            mostrarAlerta('¡Venta registrada exitosamente! Total: ' + resultados.length + ' entradas', 'success');

            // Mostrar botón de imprimir
            document.getElementById('btn-imprimir').style.display = 'block';

            // Deshabilitar botón de registrar
            btn.style.display = 'none';

            // Actualizar lista de últimas entradas
            cargarUltimasEntradas();
        } else {
            mostrarAlerta('Hubo un error al registrar algunas entradas', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión al registrar la venta', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Registrar Venta
        `;
    }
});

// Imprimir ticket
document.getElementById('btn-imprimir')?.addEventListener('click', function() {
    // Preparar contenido del ticket
    let contenido = '';
    let ticketNum = Math.floor(Math.random() * 10000); // Número de ticket aleatorio

    // Fecha y hora actual
    const ahora = new Date();
    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();
    const hora = ahora.getHours();
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const periodo = hora >= 12 ? 'p. m.' : 'a. m.';
    const hora12 = hora % 12 || 12;

    const fechaFormateada = `${dia}-${mes}-${anio}`;
    const horaFormateada = `${String(hora12).padStart(2, '0')}:${minutos} ${periodo}`;

    // Mapeo de nombres de tipos
    const nombresTipos = {
        'turista_adulto': 'No Residente (Adulto)',
        'turista_niño': 'No Residente (Niño)',
        'local': 'Residente'
    };

    // Generar un ticket por cada item y cantidad
    carrito.forEach(item => {
        for (let i = 0; i < item.cantidad; i++) {
            ticketNum++;
            contenido += `
<pre style="page-break-after: always; font-family: 'Courier New', monospace; font-size: 12px;">
         Ticket de Ingreso
   CAMPING SONRISAS COMPARTIDAS
----------------------------
Ticket #${ticketNum}
Tipo: ${nombresTipos[item.tipo] || item.nombre}

Fecha: ${fechaFormateada}
Hora: ${horaFormateada}
Precio: $${item.precio.toLocaleString('es-AR')}
----------------------------
</pre>
            `;
        }
    });

    document.getElementById('ticket-contenido').innerHTML = contenido;

    // Mostrar el ticket temporalmente para impresión
    const ticketDiv = document.getElementById('ticket-print');
    ticketDiv.style.display = 'block';

    // Imprimir
    setTimeout(() => {
        window.print();

        // Ocultar el ticket después de imprimir
        ticketDiv.style.display = 'none';

        // Limpiar carrito después de imprimir
        setTimeout(() => {
            carrito = [];
            actualizarCarrito();
            document.getElementById('btn-imprimir').style.display = 'none';
            document.getElementById('btn-registrar').style.display = 'block';
        }, 500);
    }, 100);
});

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alerta');
    alerta.textContent = mensaje;
    alerta.className = `alert alert-${tipo}`;
    alerta.classList.remove('d-none');

    setTimeout(() => {
        alerta.classList.add('d-none');
    }, 5000);
}

// Cargar últimas entradas del día
async function cargarUltimasEntradas() {
    try {
        const response = await fetch('php/obtener_entradas_hoy.php');
        const result = await response.json();

        const tbody = document.getElementById('tabla-ultimas-entradas');

        if (!result.success || result.data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-gray-500">
                        No hay entradas registradas hoy
                    </td>
                </tr>
            `;
            return;
        }

        const nombresTipos = {
            'turista_adulto': 'No Residente (Adulto)',
            'turista_niño': 'No Residente (Niño)',
            'turista_jubilado': 'No Residente (Jubilado)',
            'local': 'Residente',
            'local_adulto': 'Residente (Adulto)',
            'local_niño': 'Residente (Niño)',
            'local_jubilado': 'Residente (Jubilado)'
        };

        let html = '';
        result.data.forEach(entrada => {
            const nombreTipo = nombresTipos[entrada.tipo_entrada] || entrada.tipo_entrada;
            const estadoBadge = entrada.estado === 'activo'
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Anulado</span>';

            const botones = entrada.estado === 'activo'
                ? `<div class="d-flex gap-1">
                       <button class="btn btn-sm btn-outline-primary" onclick='reimprimirTicket(${JSON.stringify(entrada)})' title="Reimprimir">
                           <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                           </svg>
                       </button>
                       <button class="btn btn-sm btn-outline-danger" onclick="confirmarAnular(${entrada.id})" title="Anular">
                           <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                           </svg>
                       </button>
                   </div>`
                : '<span class="text-muted">—</span>';

            html += `
                <tr>
                    <td>${entrada.hora}</td>
                    <td>${nombreTipo}</td>
                    <td>$${entrada.precio.toLocaleString('es-AR')}</td>
                    <td>${estadoBadge}</td>
                    <td>${botones}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

    } catch (error) {
        console.error('Error al cargar entradas:', error);
        document.getElementById('tabla-ultimas-entradas').innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    Error al cargar las entradas
                </td>
            </tr>
        `;
    }
}

// Confirmar anulación de entrada
function confirmarAnular(entradaId) {
    entradaIdAAnular = entradaId;
    const modal = new bootstrap.Modal(document.getElementById('modalAnularEntrada'));
    modal.show();
}

// Anular entrada
async function anularEntrada(entradaId) {
    try {
        const formData = new FormData();
        formData.append('entrada_id', entradaId);

        const response = await fetch('php/anular_entrada.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            mostrarAlerta('Entrada anulada correctamente', 'success');
            cargarUltimasEntradas(); // Recargar la lista
        } else {
            mostrarAlerta('Error: ' + result.error, 'danger');
        }

    } catch (error) {
        console.error('Error al anular entrada:', error);
        mostrarAlerta('Error al anular la entrada', 'danger');
    }
}

// Reimprimir ticket de una entrada
function reimprimirTicket(entrada) {
    const nombreTipo = nombres[entrada.tipo_entrada] || entrada.tipo_entrada;

    // Formatear fecha y hora
    const fechaHora = new Date(entrada.fecha_hora);
    const dia = fechaHora.getDate().toString().padStart(2, '0');
    const mes = (fechaHora.getMonth() + 1).toString().padStart(2, '0');
    const año = fechaHora.getFullYear();
    const hora = fechaHora.getHours().toString().padStart(2, '0');
    const minutos = fechaHora.getMinutes().toString().padStart(2, '0');

    const fechaFormateada = `${dia}/${mes}/${año}`;
    const horaFormateada = `${hora}:${minutos}`;

    // Generar contenido del ticket
    const contenido = `
        <pre style="margin: 0; padding: 0; font-size: 12px; line-height: 1.4;">
=============================
  CAMPING SONRISAS
    COMPARTIDAS
=============================

Entrada #${entrada.id}
${nombreTipo}

Fecha: ${fechaFormateada}
Hora: ${horaFormateada}
Precio: $${parseFloat(entrada.precio).toLocaleString('es-AR')}
-----------------------------
</pre>
    `;

    document.getElementById('ticket-contenido').innerHTML = contenido;

    // Mostrar el ticket temporalmente para impresión
    const ticketDiv = document.getElementById('ticket-print');
    ticketDiv.style.display = 'block';

    // Imprimir
    setTimeout(() => {
        window.print();

        // Ocultar el ticket después de imprimir
        setTimeout(() => {
            ticketDiv.style.display = 'none';
        }, 500);
    }, 100);
}

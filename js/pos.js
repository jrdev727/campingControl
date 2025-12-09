// Sistema de Punto de Venta
let carrito = [];

const precios = {
    'turista_adulto': 8000,
    'turista_niño': 5000,
    'local': 3000
};

const nombres = {
    'turista_adulto': 'No Residente (Adulto)',
    'turista_niño': 'No Residente (Niño)',
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

    // Imprimir
    window.print();

    // Limpiar carrito después de imprimir
    setTimeout(() => {
        carrito = [];
        actualizarCarrito();
        document.getElementById('btn-imprimir').style.display = 'none';
        document.getElementById('btn-registrar').style.display = 'block';
        mostrarAlerta('Carrito limpiado. Listo para nueva venta.', 'success');
    }, 500);
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

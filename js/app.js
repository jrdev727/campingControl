// Función para manejar el formulario de login
document.getElementById('formulario-login')?.addEventListener('submit', function (e) {
    e.preventDefault();

    // UI Elements
    const form = this;
    const btn = form.querySelector('button[type="submit"]');
    const spinner = document.getElementById('btn-spinner');
    const btnText = document.getElementById('btn-text');
    const alerta = document.getElementById('alerta');

    // Reset Alert
    alerta.classList.add('d-none');
    alerta.className = 'alert d-none mb-4 shadow-sm border-0';

    // Loading State
    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = "Verificando...";

    const formData = new FormData(this);

    fetch('php/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            alerta.classList.remove('d-none');

            if (data.success) {
                alerta.textContent = "¡Bienvenido! Redirigiendo...";
                alerta.classList.add('alert-success');

                // Keep disabled while redirecting
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 800);
            } else {
                alerta.textContent = data.message;
                alerta.classList.add('alert-danger');

                // Reset Loading State
                btn.disabled = false;
                spinner.classList.add('d-none');
                btnText.textContent = "Ingresar al Sistema";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alerta.textContent = "Error de conexión. Intente nuevamente.";
            alerta.classList.remove('d-none');
            alerta.classList.add('alert-warning');

            // Reset Loading State
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = "Ingresar al Sistema";
        });
});

// Toggle Password Visibility
document.getElementById('togglePassword')?.addEventListener('click', function () {
    const passwordInput = document.getElementById('contraseña');
    const iconEye = document.getElementById('icon-eye');
    const iconEyeSlash = document.getElementById('icon-eye-slash');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        iconEye.classList.add('d-none');
        iconEyeSlash.classList.remove('d-none');
        this.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
        passwordInput.type = 'password';
        iconEye.classList.remove('d-none');
        iconEyeSlash.classList.add('d-none');
        this.setAttribute('aria-label', 'Mostrar contraseña');
    }
});

// Función para manejar el formulario de registro de ingresos
document.getElementById('formulario-ingreso')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('php/control_acceso.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const alerta = document.getElementById('alerta');
            alerta.classList.remove('d-none');

            if (data.success) {
                alerta.textContent = "Ingreso registrado correctamente.";
                alerta.classList.add('alert-success');
                alerta.classList.remove('alert-danger');
            } else {
                alerta.textContent = data.message;
                alerta.classList.add('alert-danger');
                alerta.classList.remove('alert-success');
            }
        })
        .catch(error => console.error('Error:', error));
});

// Función para manejar el formulario de reserva de quincho
document.getElementById('reserva-quincho')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('php/alquileres_backend.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const alerta = document.getElementById('alerta');
            alerta.classList.remove('d-none');

            if (data.success) {
                alerta.textContent = data.message;
                alerta.classList.add('alert-success');
                alerta.classList.remove('alert-danger');
            } else {
                alerta.textContent = data.message;
                alerta.classList.add('alert-danger');
                alerta.classList.remove('alert-success');
            }
        })
        .catch(error => console.error('Error:', error));
});

// Función para manejar el formulario de alquiler de reposera
document.getElementById('alquiler-reposera')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('php/alquileres_backend.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const alerta = document.getElementById('alerta');
            alerta.classList.remove('d-none');

            if (data.success) {
                alerta.textContent = data.message;
                alerta.classList.add('alert-success');
                alerta.classList.remove('alert-danger');
            } else {
                alerta.textContent = data.message;
                alerta.classList.add('alert-danger');
                alerta.classList.remove('alert-success');
            }
        })
        .catch(error => console.error('Error:', error));
});
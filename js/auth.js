const API_BASE_URL = 'https://api.jonaramdev.com/php'; // API del VPS de Digital Ocean

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Inyectar HTML de Sidebar y Header
    await loadComponent('sidebar-container', 'includes/sidebar.html');
    await loadComponent('header-container', 'includes/header.html');

    // 2. Verificar Autenticación (Token)
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        if (!window.location.pathname.endsWith('login.html')) {
            window.location.href = 'login.html';
        }
        return; // Detener la ejecución si estamos en login y no hay token
    }

    // 3. Decodificar Token y configurar UI (Ejemplo simple, asumiendo JWT o JSON guardado)
    try {
        const userInfo = JSON.parse(localStorage.getItem('user_info'));
        if (userInfo) {
            setupUI(userInfo);
        }
    } catch (e) {
        console.error('Error parseando user_info', e);
    }

    // 4. Configurar Logout
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_info');
            window.location.href = 'login.html';
        });
    }
});

async function loadComponent(containerId, url) {
    const container = document.getElementById(containerId);
    if (!container) return;
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const html = await response.text();
        container.innerHTML = html;
    } catch (error) {
        console.error(`Error loading ${url}:`, error);
        container.innerHTML = `<div class="alert alert-danger">Error cargando componente</div>`;
    }
}

function setupUI(userInfo) {
    // Configurar Header
    const nameEl = document.getElementById('header-user-name');
    const roleEl = document.getElementById('header-user-role');
    const avatarEl = document.getElementById('header-user-avatar');
    
    if (nameEl) nameEl.textContent = userInfo.usuario || 'Usuario';
    if (roleEl) roleEl.textContent = userInfo.rol ? (userInfo.rol.charAt(0).toUpperCase() + userInfo.rol.slice(1)) : 'Usuario';
    
    if (avatarEl) {
        let inicial = 'U';
        if (userInfo.usuario) inicial = userInfo.usuario.charAt(0).toUpperCase();
        else if (userInfo.rol === 'administrador') inicial = 'A';
        avatarEl.textContent = inicial;
    }

    // Configurar Sidebar según Rol
    const adminMenu = document.getElementById('admin-menu-section');
    const regularMenu = document.getElementById('regular-menu-section');
    
    if (userInfo.rol === 'administrador') {
        if (adminMenu) adminMenu.style.display = 'block';
        if (regularMenu) regularMenu.style.display = 'none';
        
        // Proteger página de admin
        if (window.location.pathname.endsWith('admin.html') && userInfo.rol !== 'administrador') {
            window.location.href = 'index.html';
        }
    } else {
        if (adminMenu) adminMenu.style.display = 'none';
        if (regularMenu) regularMenu.style.display = 'block';
    }

    // Configurar Título y Menú Activo
    const pageTitle = document.getElementById('header-page-title');
    const path = window.location.pathname;
    
    if (path.endsWith('admin.html')) {
        if (pageTitle) pageTitle.textContent = 'Dashboard Principal';
        const link = document.getElementById('link-admin');
        if (link) link.classList.add('active');
    } else if (path.endsWith('reportes.html')) {
        if (pageTitle) pageTitle.textContent = 'Reportes y Estadísticas';
        const link = document.getElementById('link-reportes');
        if (link) link.classList.add('active');
    } else {
        if (pageTitle) pageTitle.textContent = 'Punto de Venta';
        const link = document.getElementById('link-control');
        if (link) link.classList.add('active');
    }
}

// Global Headers Setup for Fetch requests
window.fetchWithAuth = async function(url, options = {}) {
    const token = localStorage.getItem('jwt_token');
    
    const headers = new Headers(options.headers || {});
    if (token) {
        headers.set('Authorization', `Bearer ${token}`);
    }
    
    return fetch(url, { ...options, headers });
};

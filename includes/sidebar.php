<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-header">
        <a href="<?php echo ($esAdmin) ? 'admin.php' : 'index.php'; ?>" class="sidebar-logo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="32" height="32" rx="8" fill="currentColor"/>
                <path d="M16 8L20 12H12L16 8Z" fill="white"/>
                <path d="M10 14H22V24H10V14Z" fill="white"/>
                <circle cx="13" cy="18" r="1" fill="currentColor"/>
                <circle cx="19" cy="18" r="1" fill="currentColor"/>
            </svg>
            Camping Sonrisas
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php if ($esAdmin): ?>
            <!-- Menú Admin -->
            <div class="sidebar-section-title">Dashboard</div>
            <div class="sidebar-item">
                <a href="admin.php" class="sidebar-link <?php echo ($paginaActual == 'admin') ? 'active' : ''; ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard Principal
                </a>
            </div>

            <div class="sidebar-section-title">Gestión</div>
        <?php else: ?>
            <div class="sidebar-section-title">Menú Principal</div>
        <?php endif; ?>

        <div class="sidebar-item">
            <a href="index.php" class="sidebar-link <?php echo ($paginaActual == 'control') ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Punto de Venta
            </a>
        </div>

        <div class="sidebar-section-title">Otros</div>
        <div class="sidebar-item">
            <a href="reportes.php" class="sidebar-link <?php echo ($paginaActual == 'reportes') ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reportes
            </a>
        </div>

        <!-- Logout -->
        <div class="sidebar-item" style="margin-top: auto; padding-top: 20px; border-top: 1px solid var(--gray-200);">
            <a href="logout.php" class="sidebar-link" style="color: var(--error-500);">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar Sesión
            </a>
        </div>
    </nav>
</aside>

<!-- Backdrop for mobile -->
<div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    sidebar.classList.toggle('mobile-open');
    if (sidebar.classList.contains('mobile-open')) {
        backdrop.style.display = 'block';
    } else {
        backdrop.style.display = 'none';
    }
}
</script>

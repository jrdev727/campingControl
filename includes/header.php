<!-- Header -->
<header class="app-header">
    <div class="d-flex align-items-center gap-3">
        <!-- Mobile Menu Toggle -->
        <button class="btn btn-link d-lg-none p-0 mobile-menu-toggle" onclick="toggleSidebar()" style="border: none; background: none;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <h1 class="header-title"><?php echo $tituloPagina; ?></h1>
    </div>

    <div class="header-actions">
        <!-- User Menu -->
        <div class="user-menu">
            <div class="user-avatar">
                <?php
                    // Obtener inicial del usuario
                    $inicial = 'U';
                    if (isset($_SESSION['usuario'])) {
                        $inicial = strtoupper(substr($_SESSION['usuario'], 0, 1));
                    } elseif (isset($_SESSION['rol'])) {
                        $inicial = ($_SESSION['rol'] === 'administrador') ? 'A' : 'U';
                    }
                    echo $inicial;
                ?>
            </div>
            <div class="d-none d-md-block">
                <div style="font-size: 14px; font-weight: 500; color: var(--gray-900);">
                    <?php
                        echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
                    ?>
                </div>
                <div style="font-size: 12px; color: var(--gray-500);">
                    <?php
                        echo isset($_SESSION['rol']) ? ucfirst($_SESSION['rol']) : 'Usuario';
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>

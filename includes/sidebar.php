<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="#" class="brand-link">
                    <span class="brand-text fw-light">CRM Funeraria</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">
                        <?php if($_SESSION['rol'] === 'Vendedor'): ?>
                        <li class="nav-item">
                            <a href="/DWYM-php/modules/vendedor/dashboard.php" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Mi Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/DWYM-php/modules/vendedor/prospectos.php" class="nav-link">
                                <i class="nav-icon bi bi-people"></i>
                                <p>Mis Prospectos</p>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if($_SESSION['rol'] === 'Administrador'): ?>
                        <li class="nav-item">
                            <a href="/DWYM-php/modules/admin/catalogo.php" class="nav-link">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p>Catálogo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/DWYM-php/modules/admin/metas.php" class="nav-link">
                                <i class="nav-icon bi bi-bullseye"></i>
                                <p>Metas Corporativas</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </aside>
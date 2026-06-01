<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['rol'])) {
    header('Location: /DWYM-php/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Funeraria UDP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        /* ==========================================
           PARCHE CSS CORREGIDO: Ocultar enlaces de AdminLTE
           ========================================== */
        a.skip-link, 
        a[href^="#navigation"], 
        a[href^="#main-content"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            z-index: -9999 !important;
            pointer-events: none !important;
        }

        .udp-top-bar {
            background-color: #3b4b6b;
            color: white;
            padding: 6px 20px;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .udp-main-header {
            background-color: #273752 !important;
            border-bottom: none;
        }
        .udp-brand-text {
            font-family: 'Georgia', 'Times New Roman', serif;
            color: white;
            line-height: 1.1;
            margin-left: 10px;
            letter-spacing: 0.5px;
        }
        .udp-nav-links {
            display: flex;
            gap: 25px;
            margin: 0 auto;
        }
        .udp-nav-item {
            color: #d1d9e6 !important;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 5px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }
        .udp-nav-item:hover, .udp-nav-item.active {
            border-bottom: 3px solid #aebdd6;
            color: white !important;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

    <nav class="app-header navbar navbar-expand flex-column p-0 udp-main-header" data-bs-theme="dark">
        
        <div class="udp-top-bar d-none d-md-flex shadow-sm">
            <div>
                <i class="bi bi-clock me-1"></i> Atención Sistema Interno 24/7
            </div>
            <div>
                <i class="bi bi-telephone me-1"></i> Soporte TI: +569 9999 9999
                <span class="ms-3 border-start border-secondary ps-3">
                    <i class="bi bi-instagram fs-6 mx-1"></i>
                    <i class="bi bi-twitter-x fs-6 mx-1"></i>
                    <i class="bi bi-facebook fs-6 mx-1"></i>
                </span>
            </div>
        </div>

        <div class="w-100 container-fluid py-2">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-white" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list fs-4"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-flex align-items-center ms-2">
                    <i class="bi bi-shield-shaded fs-2 text-white"></i>
                    <div class="udp-brand-text">
                        <strong class="fs-5">Funeraria</strong><br>
                        <span>UDP</span>
                    </div>
                </li>
            </ul>

            <div class="udp-nav-links d-none d-lg-flex">
                <a href="/DWYM-php/modules/vendedor/dashboard.php" class="udp-nav-item active">VISTA GENERAL</a>
                <a href="/DWYM-php/modules/vendedor/prospectos.php" class="udp-nav-item">DIRECTORIO CLIENTES</a>
                <a href="#" class="udp-nav-item">CATÁLOGO RÁPIDO</a>
                <a href="#" class="udp-nav-item">SOPORTE TI</a>
            </div>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-person-circle fs-5 me-2"></i>
                        <span class="d-none d-md-inline fw-bold">
                            <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['email'] ?? 'Usuario') ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/DWYM-php/modules/auth/logout.php" class="nav-link text-danger" title="Cerrar Sesión">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
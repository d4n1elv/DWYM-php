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
<body class="layout-fixed sidebar-collapse sidebar-mini bg-body-tertiary">
<div class="app-wrapper">

    <nav class="main-header navbar navbar-expand" style="background-color: #f4f9f6; border-bottom: 2px solid #b8dacb;">
    <div class="container-fluid">
        <a href="#" class="navbar-brand text-dark fw-bold d-flex align-items-center">
            <i class="bi bi-tree-fill me-2" style="color: #63a388; font-size: 1.5rem;"></i>
            <span style="color: #2c4c3b; letter-spacing: 0.5px;">Senderos de Paz</span>
        </a>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <span class="nav-link text-dark fw-medium">
                    <i class="bi bi-person-circle me-1" style="color: #63a388;"></i> 
                    Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                </span>
            </li>
            <li class="nav-item">
                <a href="/DWYM-php/logout.php" class="nav-link text-danger fw-bold">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </li>
        </ul>
    </div>
</nav>
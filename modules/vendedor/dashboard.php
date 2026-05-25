<?php
// modules/vendedor/dashboard.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Seguridad: Solo Vendedor
if ($_SESSION['rol'] !== 'Vendedor') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$vendedor_id = 2; // Por ahora lo forzamos al ID 2 (Juan Vendedor) simulando el login

// 1. Traemos las metas corporativas
$query_metas = "SELECT * FROM metas_corporativas";
$stmt_metas = $db->query($query_metas);
$metas_db = $stmt_metas->fetchAll(PDO::FETCH_ASSOC);

// Formateamos las metas en un array fácil de usar
$metas = [];
foreach($metas_db as $m) {
    $metas[$m['etapa']] = $m;
}

// Nombres de las etapas para la interfaz
$titulos_etapas = [
    1 => 'Prospectos Nuevos',
    2 => 'Reuniones Agendadas',
    3 => 'Citas Realizadas',
    4 => 'Ventas Cerradas'
];

// Iconos de Bootstrap para cada tarjeta
$iconos_etapas = [
    1 => 'bi-person-plus-fill',
    2 => 'bi-calendar-check-fill',
    3 => 'bi-people-fill',
    4 => 'bi-cash-coin'
];

// 2. Aquí a futuro contaremos cuántos registros hizo el vendedor HOY.
// Por ahora simularemos que el vendedor ha hecho algunos avances para que veas los colores:
$progreso_hoy = [
    1 => 5,  // Hizo 5 prospectos hoy
    2 => 2,  // Agendó 2 reuniones
    3 => 0,  // Hizo 0 citas
    4 => 1   // Cerró 1 venta
];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Mi Rendimiento Diario</h3>
            <p class="text-muted">¡Vamos con todo hoy, <?= $_SESSION['nombre_usuario'] ?>!</p>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <?php for($i = 1; $i <= 4; $i++): 
                    // Si el admin no ha configurado la meta, ponemos valores por defecto para no romper el sistema
                    $meta_esperada = isset($metas[$i]) ? $metas[$i]['meta_diaria'] : 1; 
                    $logrado = $progreso_hoy[$i];
                    
                    // Calculamos el porcentaje de logro
                    $porcentaje = ($logrado / $meta_esperada) * 100;
                    
                    // Lógica del Semáforo corporativo
                    $color_bg = 'text-bg-secondary'; // Por defecto (gris)
                    if(isset($metas[$i])) {
                        if($porcentaje >= $metas[$i]['rango_verde_min']) {
                            $color_bg = 'text-bg-success'; // Verde
                        } elseif($porcentaje >= $metas[$i]['rango_amarillo_min']) {
                            $color_bg = 'text-bg-warning'; // Amarillo
                        } else {
                            $color_bg = 'text-bg-danger'; // Rojo
                        }
                    }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box <?= $color_bg ?> mb-4 shadow">
                        <div class="inner">
                            <h3><?= $logrado ?> <sup class="fs-5">/ <?= $meta_esperada ?></sup></h3>
                            <p><?= $titulos_etapas[$i] ?></p>
                            <small class="fw-bold">Llevas <?= $logrado ?> de <?= $meta_esperada ?> hoy</small>
                        </div>
                        <div class="small-box-icon">
                            <i class="bi <?= $iconos_etapas[$i] ?> opacity-50 fs-1"></i>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3 class="card-title">Resumen Semanal</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Los gráficos interactivos se habilitarán cuando tengamos registros de clientes reales.</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
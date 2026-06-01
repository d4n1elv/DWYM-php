<?php
// modules/vendedor/dashboard.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

if ($_SESSION['rol'] !== 'Vendedor') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$vendedor_id = 2; // Simulado para Juan Vendedor

// 1. Traemos las metas del Administrador (Base)
$stmt_admin = $db->query("SELECT * FROM metas_corporativas");
$metas_admin = [];
foreach($stmt_admin->fetchAll(PDO::FETCH_ASSOC) as $m) {
    $metas_admin[$m['etapa']] = $m;
}

// 2. Traemos las metas personalizadas del Vendedor (Prioridad) [cite: 75-79]
$stmt_vend = $db->prepare("SELECT * FROM metas_vendedor WHERE vendedor_id = :id");
$stmt_vend->execute([':id' => $vendedor_id]);
$metas_vend = [];
foreach($stmt_vend->fetchAll(PDO::FETCH_ASSOC) as $m) {
    $metas_vend[$m['etapa']] = $m;
}

// 3. Contamos cuántos prospectos reales tiene el vendedor en cada etapa
$stmt_progreso = $db->prepare("SELECT etapa, COUNT(*) as total FROM prospectos WHERE vendedor_id = :id GROUP BY etapa");
$stmt_progreso->execute([':id' => $vendedor_id]);
$progreso_real = [1 => 0, 2 => 0, 3 => 0, 4 => 0]; // Por defecto en 0
foreach($stmt_progreso->fetchAll(PDO::FETCH_ASSOC) as $p) {
    $progreso_real[$p['etapa']] = $p['total'];
}

$titulos_etapas = [1 => 'Prospectos', 2 => 'Agendados', 3 => 'Citas', 4 => 'Ventas'];
$iconos_etapas = [1 => 'bi-person-plus-fill', 2 => 'bi-calendar-check-fill', 3 => 'bi-people-fill', 4 => 'bi-cash-coin'];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Mi Rendimiento en Tiempo Real</h3>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <?php for($i = 1; $i <= 4; $i++): 
                    // LÓGICA DE PRIORIDAD: Si el vendedor tiene meta, se usa esa. Si no, la del admin. Si ninguna existe, ponemos 1 para evitar dividir por 0.
                    $meta_diaria = $metas_vend[$i]['meta_diaria'] ?? ($metas_admin[$i]['meta_diaria'] ?? 1);
                    $rango_verde = $metas_vend[$i]['rango_verde_min'] ?? ($metas_admin[$i]['rango_verde_min'] ?? 80);
                    $rango_amarillo = $metas_vend[$i]['rango_amarillo_min'] ?? ($metas_admin[$i]['rango_amarillo_min'] ?? 41);
                    
                    $logrado = $progreso_real[$i];
                    $porcentaje = ($logrado / $meta_diaria) * 100;
                    
                    // Cálculo del semáforo
                    if ($porcentaje >= $rango_verde) {
                        $color_bg = 'text-bg-success';
                    } elseif ($porcentaje >= $rango_amarillo) {
                        $color_bg = 'text-bg-warning';
                    } else {
                        $color_bg = 'text-bg-danger';
                    }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box <?= $color_bg ?> mb-4 shadow-sm">
                        <div class="inner">
                            <h3><?= $logrado ?> <sup class="fs-5">/ <?= $meta_diaria ?></sup></h3>
                            <p><?= $titulos_etapas[$i] ?></p>
                            <small class="fw-bold">Llevas <?= $logrado ?> de <?= $meta_diaria ?> hoy</small>
                        </div>
                        <div class="small-box-icon">
                            <i class="bi <?= $iconos_etapas[$i] ?> opacity-50 fs-1"></i>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title">Distribución del Embudo</h3>
                        </div>
                        <div class="card-body">
                            <div id="grafico-embudo"></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        // Le pasamos los datos reales desde PHP usando json_encode
        series: <?= json_encode(array_values($progreso_real)) ?>,
        labels: ['Etapa 1 (Prospectos)', 'Etapa 2 (Agendados)', 'Etapa 3 (Citas)', 'Etapa 4 (Ventas)'],
        chart: { type: 'donut', height: 350 },
        colors: ['#6c757d', '#0dcaf0', '#0d6efd', '#198754'],
        plotOptions: {
            pie: { donut: { size: '65%' } }
        },
        dataLabels: { enabled: true }
    };

    var chart = new ApexCharts(document.querySelector("#grafico-embudo"), options);
    chart.render();
});
</script>
<?php
// modules/vendedor/config_semaforos.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

if ($_SESSION['rol'] !== 'Vendedor') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$vendedor_id = 2; // Simulado para Juan Vendedor

// 1. Traemos las metas base del ADMIN (para mostrarlas como referencia)
$stmt_admin = $db->query("SELECT * FROM metas_corporativas");
$metas_admin = [];
foreach($stmt_admin->fetchAll(PDO::FETCH_ASSOC) as $m) {
    $metas_admin[$m['etapa']] = $m;
}

// 2. Traemos la configuración personal del VENDEDOR (si existe)
$stmt_vend = $db->prepare("SELECT * FROM metas_vendedor WHERE vendedor_id = :id");
$stmt_vend->execute([':id' => $vendedor_id]);
$metas_personales = [];
foreach($stmt_vend->fetchAll(PDO::FETCH_ASSOC) as $m) {
    $metas_personales[$m['etapa']] = $m;
}

$nombres_etapas = [
    1 => 'Etapa 1: Prospecto',
    2 => 'Etapa 2: Agendar',
    3 => 'Etapa 3: Cita',
    4 => 'Etapa 4: Venta'
];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Mis Semáforos Personalizados</h3>
            <p class="text-muted">Ajusta tus propios umbrales de rendimiento (Esta configuración sobrescribirá la vista corporativa solo para ti).</p>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <form action="config_semaforos_action.php" method="POST">
                <div class="row">
                    <?php for($i = 1; $i <= 4; $i++): 
                        // Mostramos el valor personal si existe, si no, mostramos el del Admin como base
                        $val_verde = $metas_personales[$i]['rango_verde_min'] ?? ($metas_admin[$i]['rango_verde_min'] ?? 80);
                        $val_amarillo = $metas_personales[$i]['rango_amarillo_min'] ?? ($metas_admin[$i]['rango_amarillo_min'] ?? 41);
                        $val_rojo = $metas_personales[$i]['rango_rojo_max'] ?? ($metas_admin[$i]['rango_rojo_max'] ?? 40);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-bg-secondary">
                                <h5 class="card-title mb-0"><?= $nombres_etapas[$i] ?></h5>
                            </div>
                            <div class="card-body">
                                <h6>Mis Rangos de Semáforo (%)</h6>
                                <div class="row g-2 align-items-center mt-2">
                                    <div class="col-4">
                                        <label class="form-label text-success">Verde (Min)</label>
                                        <input type="number" class="form-control border-success" name="meta[<?= $i ?>][verde]" value="<?= $val_verde ?>" required min="0" max="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-warning">Amarillo (Min)</label>
                                        <input type="number" class="form-control border-warning" name="meta[<?= $i ?>][amarillo]" value="<?= $val_amarillo ?>" required min="0" max="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-danger">Rojo (Max)</label>
                                        <input type="number" class="form-control border-danger" name="meta[<?= $i ?>][rojo]" value="<?= $val_rojo ?>" required min="0" max="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <div class="text-end mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Guardar Mi Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
<?php
// modules/admin/metas.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Seguridad: Solo Admin
if ($_SESSION['rol'] !== 'Administrador') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();

// Traemos las metas actuales si existen
$query = "SELECT * FROM metas_corporativas";
$stmt = $db->query($query);
$metas_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparamos un arreglo con valores por defecto por si la tabla está vacía
$metas = [
    1 => ['meta_diaria' => 0, 'rango_verde_min' => 80, 'rango_amarillo_min' => 41, 'rango_rojo_max' => 40],
    2 => ['meta_diaria' => 0, 'rango_verde_min' => 80, 'rango_amarillo_min' => 41, 'rango_rojo_max' => 40],
    3 => ['meta_diaria' => 0, 'rango_verde_min' => 80, 'rango_amarillo_min' => 41, 'rango_rojo_max' => 40],
    4 => ['meta_diaria' => 0, 'rango_verde_min' => 80, 'rango_amarillo_min' => 41, 'rango_rojo_max' => 40],
];

// Si hay datos en la BD, sobrescribimos los valores por defecto
foreach($metas_db as $m) {
    $metas[$m['etapa']] = $m;
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
            <h3 class="mb-0">Configuración de Metas Corporativas</h3>
            <p class="text-muted">Define los objetivos diarios y los umbrales porcentuales para evaluar a los vendedores.</p>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <form action="metas_action.php" method="POST">
                <div class="row">
                    <?php for($i = 1; $i <= 4; $i++): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-bg-dark">
                                <h5 class="card-title mb-0"><?= $nombres_etapas[$i] ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Meta Diaria (Cantidad)</label>
                                    <input type="number" class="form-control" name="meta[<?= $i ?>][diaria]" value="<?= $metas[$i]['meta_diaria'] ?>" required min="1">
                                </div>
                                <hr>
                                <h6>Rangos del Semáforo (%)</h6>
                                <div class="row g-2 align-items-center">
                                    <div class="col-4">
                                        <label class="form-label text-success">Verde (Min)</label>
                                        <input type="number" class="form-control border-success" name="meta[<?= $i ?>][verde]" value="<?= $metas[$i]['rango_verde_min'] ?>" required min="0" max="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-warning">Amarillo (Min)</label>
                                        <input type="number" class="form-control border-warning" name="meta[<?= $i ?>][amarillo]" value="<?= $metas[$i]['rango_amarillo_min'] ?>" required min="0" max="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-danger">Rojo (Max)</label>
                                        <input type="number" class="form-control border-danger" name="meta[<?= $i ?>][rojo]" value="<?= $metas[$i]['rango_rojo_max'] ?>" required min="0" max="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <div class="text-end mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Guardar Metas Corporativas
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
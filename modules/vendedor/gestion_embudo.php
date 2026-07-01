<?php
// modules/vendedor/gestion_embudo.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

if ($_SESSION['rol'] !== 'Vendedor' || !isset($_GET['id'])) {
    header('Location: prospectos.php');
    exit;
}

$db = (new Database())->getConnection();
$prospecto_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

$query = "SELECT * FROM prospectos WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $prospecto_id]);
$prospecto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prospecto) {
    die("Prospecto no encontrado.");
}

$etapa_actual = $prospecto['etapa'];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Gestión de Prospecto: <?= htmlspecialchars($prospecto['nombre']) ?></h3>
            <a href="prospectos.php" class="btn btn-sm btn-secondary mt-2"><i class="bi bi-arrow-left"></i> Volver al listado</a>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-4">Progreso del Embudo</h5>
                    <div class="d-flex justify-content-between position-relative w-75 mx-auto">
                        <div class="position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; background-color: #e9ecef; z-index: 0;"></div>
                        
                        <?php 
                        $pasos = [1 => 'Prospecto', 2 => 'Agendar', 3 => 'Cita', 4 => 'Venta'];
                        foreach($pasos as $num => $nombre_paso): 
                            $color = ($num <= $etapa_actual) ? 'primary' : 'secondary';
                        ?>
                        <div class="position-relative" style="z-index: 1;">
                            <div class="rounded-circle text-bg-<?= $color ?> d-flex align-items-center justify-content-center border border-white border-3 mx-auto" style="width: 40px; height: 40px; font-weight: bold;">
                                <?= $num ?>
                            </div>
                            <div class="mt-2 fw-bold text-<?= $color ?>"><?= $nombre_paso ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-bg-dark">
                            <h5 class="card-title mb-0"><i class="bi bi-person-badge"></i> Datos del Cliente</h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Nombre:</strong> <?= htmlspecialchars($prospecto['nombre']) ?></li>
                            <li class="list-group-item"><strong>Comuna:</strong> <?= htmlspecialchars($prospecto['comuna']) ?></li>
                            <li class="list-group-item"><strong>Teléfono:</strong> <?= htmlspecialchars($prospecto['telefono']) ?></li>
                            <?php if($prospecto['correo']): ?>
                                <li class="list-group-item"><strong>Correo:</strong> <?= htmlspecialchars($prospecto['correo']) ?></li>
                            <?php endif; ?>
                            <?php if($prospecto['rut']): ?>
                                <li class="list-group-item"><strong>RUT:</strong> <?= htmlspecialchars($prospecto['rut']) ?></li>
                                <li class="list-group-item"><strong>Género:</strong> <?= htmlspecialchars($prospecto['genero']) ?></li>
                                <li class="list-group-item"><strong>Nacimiento:</strong> <?= date('d/m/Y', strtotime($prospecto['fecha_nacimiento'])) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-primary">
                        <div class="card-header text-bg-primary">
                            <h5 class="card-title mb-0">Acción Requerida</h5>
                        </div>
                        <div class="card-body">
                            
                            <?php if($etapa_actual == 1): ?>
                                <h5><i class="bi bi-calendar-plus"></i> Agendar Reunión (Avanzar a Etapa 2)</h5>
                                [cite_start]<p class="text-muted">Para agendar, debes solicitar el correo electrónico del cliente.</p>
                                <form action="embudo_action.php" method="POST">
                                    <input type="hidden" name="accion" value="avanzar_etapa_2">
                                    <input type="hidden" name="prospecto_id" value="<?= $prospecto['id'] ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Correo Electrónico *</label>
                                            <input type="email" class="form-control" name="correo" required placeholder="ejemplo@correo.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Fecha y Hora de la Cita *</label>
                                            <input type="datetime-local" class="form-control" name="fecha_hora_cita" required>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Guardar Cita y Avanzar</button>
                                    </div>
                                </form>

                            <?php elseif($etapa_actual == 2): ?>
                                <h5><i class="bi bi-people-fill"></i> Concretar Cita (Avanzar a Etapa 3)</h5>
                                <p class="text-muted">La reunión fue agendada. Para marcarla como realizada, exige los datos duros.</p>
                                <form action="embudo_action.php" method="POST">
                                    <input type="hidden" name="accion" value="avanzar_etapa_3">
                                    <input type="hidden" name="prospecto_id" value="<?= $prospecto['id'] ?>">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">RUT *</label>
                                            <input type="text" class="form-control" name="rut" required placeholder="12345678-9">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Género *</label>
                                            <select class="form-select" name="genero" required>
                                                <option value="" disabled selected>Seleccione...</option>
                                                <option value="M">Masculino</option>
                                                <option value="F">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Fecha de Nac. *</label>
                                            <input type="date" class="form-control" name="fecha_nacimiento" required>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-all"></i> Marcar Cita Realizada</button>
                                    </div>
                                </form>

                            <?php elseif($etapa_actual == 3): ?>
                                <h5><i class="bi bi-cash-coin"></i> Cerrar Venta (Etapa Final)</h5>
                                [cite_start]<p class="text-muted">Sube el documento, elige el producto y registra los 3 referidos obligatorios </p>
                                <?php 
                                $stmt_cat = $db->query("SELECT c.id, c.nombre, p.precio FROM catalogo c JOIN catalogo_precios p ON c.id = p.catalogo_id WHERE c.estado = 'Activo' AND p.id = (SELECT MAX(id) FROM catalogo_precios WHERE catalogo_id = c.id)");
                                $catalogo_items = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <form action="embudo_action.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="accion" value="cerrar_venta">
                                    <input type="hidden" name="prospecto_id" value="<?= $prospecto['id'] ?>">
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Producto Contratado *</label>
                                            <select class="form-select" name="catalogo_id" required>
                                                <option value="" disabled selected>Seleccione...</option>
                                                <?php foreach($catalogo_items as $item): ?>
                                                    <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['nombre']) ?> - $<?= number_format($item['precio'], 0, ',', '.') ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Documento Firmado / CI *</label>
                                            <input type="file" class="form-control" name="documento" accept=".jpg,.jpeg,.png,.pdf" required>
                                        </div>
                                    </div>

                                    <hr>
                                    [cite_start]<h6 class="fw-bold text-primary"><i class="bi bi-person-lines-fill"></i> 3 Referidos Obligatorios.</h6>
                                    <?php for($i = 1; $i <= 3; $i++): ?>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="ref_nombre[]" placeholder="Nombre Referido <?= $i ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="ref_comuna[]" placeholder="Comuna" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="ref_telefono[]" placeholder="Teléfono" required>
                                        </div>
                                    </div>
                                    <?php endfor; ?>

                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-bag-check-fill"></i> Formalizar Venta</button>
                                    </div>
                                </form>

                            <?php elseif($etapa_actual == 4): ?>
                                <div class="alert alert-success text-center">
                                    <h4><i class="bi bi-trophy-fill text-warning"></i> ¡Venta Completada!</h4>
                                    <p>Este cliente ya finalizó exitosamente su viaje por el embudo de ventas.</p>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">Error: Etapa desconocida.</div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
<?php
// modules/vendedor/prospectos.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

if ($_SESSION['rol'] !== 'Vendedor') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$vendedor_id = 2; // Simulado para Juan Vendedor

// Traemos los prospectos de este vendedor
$query = "SELECT * FROM prospectos WHERE vendedor_id = :vendedor_id ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute([':vendedor_id' => $vendedor_id]);
$prospectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper para los colores de las etapas
$colores_etapas = [
    1 => 'text-bg-secondary', // Prospecto
    2 => 'text-bg-info',      // Agendar
    3 => 'text-bg-primary',   // Cita
    4 => 'text-bg-success'    // Venta
];
$nombres_etapas = [
    1 => '1. Prospecto',
    2 => '2. Agendar',
    3 => '3. Cita',
    4 => '4. Venta Cerrada'
];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Mis Prospectos y Clientes</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProspecto">
                <i class="bi bi-person-plus-fill"></i> Nuevo Prospecto (Etapa 1)
            </button>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Contacto inicial</th>
                                <th>Etapa Actual</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($prospectos) > 0): ?>
                                <?php foreach($prospectos as $p): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td>
                                        <div><i class="bi bi-geo-alt text-danger"></i> <?= htmlspecialchars($p['comuna']) ?></div>
                                        <div><i class="bi bi-telephone text-success"></i> <?= htmlspecialchars($p['telefono']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $colores_etapas[$p['etapa']] ?> fs-6">
                                            <?= $nombres_etapas[$p['etapa']] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($p['fecha_registro'])) ?></td>
                                    <td>
                                        <a href="gestion_embudo.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Gestionar <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No tienes prospectos registrados. ¡Es hora de prospectar!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalNuevoProspecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="prospectos_action.php" method="POST">
          <div class="modal-header text-bg-primary">
            <h5 class="modal-title">Registrar Nuevo Prospecto</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="crear_etapa_1">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> En esta etapa inicial solo requerimos los datos básicos del cliente.
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Comuna</label>
                <input type="text" class="form-control" name="comuna" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Número de Teléfono</label>
                <input type="text" class="form-control" name="telefono" required placeholder="+56 9...">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Prospecto</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
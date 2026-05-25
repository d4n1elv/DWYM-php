<?php
// modules/admin/catalogo.php
require_once '../../config/database.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Verificamos por seguridad que solo el Admin entre aquí
if ($_SESSION['rol'] !== 'Administrador') {
    header('Location: /DWYM-php/index.php');
    exit;
}

// Conectamos a la BD y traemos los ítems activos
$db = (new Database())->getConnection();
// Usamos un JOIN para traer el catálogo y su último precio registrado
$query = "SELECT c.id, c.nombre, c.tipo, c.desc_corta, c.estado, p.precio 
          FROM catalogo c 
          LEFT JOIN catalogo_precios p ON c.id = p.catalogo_id 
          WHERE p.id = (SELECT MAX(id) FROM catalogo_precios WHERE catalogo_id = c.id)
          ORDER BY c.id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Gestión de Catálogo</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoItem">
                <i class="bi bi-plus-circle"></i> Nuevo Ítem
            </button>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Precio Actual</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($items) > 0): ?>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td><span class="badge text-bg-secondary"><?= $item['tipo'] ?></span></td>
                                    <td><?= htmlspecialchars($item['desc_corta']) ?></td>
                                    <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if($item['estado'] == 'Activo'): ?>
                                            <span class="badge text-bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
    <button class="btn btn-sm btn-warning btn-editar" 
            data-id="<?= $item['id'] ?>" 
            data-nombre="<?= htmlspecialchars($item['nombre']) ?>" 
            data-tipo="<?= $item['tipo'] ?>" 
            data-desc-corta="<?= htmlspecialchars($item['desc_corta']) ?>" 
            data-precio="<?= $item['precio'] ?>" 
            data-bs-toggle="modal" data-bs-target="#modalEditarItem">
        <i class="bi bi-pencil"></i>
    </button>

    <form action="catalogo_action.php" method="POST" class="d-inline">
        <input type="hidden" name="accion" value="toggle_estado">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <input type="hidden" name="estado_actual" value="<?= $item['estado'] ?>">
        <button type="submit" class="btn btn-sm <?= $item['estado'] == 'Activo' ? 'btn-danger' : 'btn-success' ?>" title="<?= $item['estado'] == 'Activo' ? 'Desactivar' : 'Activar' ?>">
            <i class="bi bi-power"></i>
        </button>
    </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No hay ítems en el catálogo aún.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalNuevoItem" tabindex="-1" aria-labelledby="modalNuevoItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="catalogo_action.php" method="POST">
          <div class="modal-header text-bg-primary">
            <h5 class="modal-title" id="modalNuevoItemLabel">Registrar Producto/Servicio</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="crear">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del Ítem</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="tipo" required>
                        <option value="Producto">Producto</option>
                        <option value="Servicio">Servicio</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción Corta</label>
                    <input type="text" class="form-control" name="desc_corta" maxlength="255" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción Larga</label>
                    <textarea class="form-control" name="desc_larga" rows="3" required></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor Inicial ($)</label>
                    <input type="number" step="0.01" class="form-control" name="precio" required>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Ítem</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditarItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="catalogo_action.php" method="POST">
          <div class="modal-header text-bg-warning">
            <h5 class="modal-title">Editar Producto/Servicio</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="precio_actual_db" id="edit_precio_actual_db">
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del Ítem</label>
                    <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="tipo" id="edit_tipo" required>
                        <option value="Producto">Producto</option>
                        <option value="Servicio">Servicio</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción Corta</label>
                    <input type="text" class="form-control" name="desc_corta" id="edit_desc_corta" maxlength="255" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor Actual ($)</label>
                    <input type="number" step="0.01" class="form-control" name="precio_nuevo" id="edit_precio" required>
                    <small class="text-muted">Si cambias el valor, se generará un nuevo registro histórico[cite: 50, 54].</small>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Actualizar Ítem</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
// Script para rellenar el Modal de Edición automáticamente cuando se hace clic en el botón
document.addEventListener('DOMContentLoaded', () => {
    const botonesEditar = document.querySelectorAll('.btn-editar');
    
    botonesEditar.forEach(boton => {
        boton.addEventListener('click', function() {
            // Capturamos los datos de los atributos 'data-*'
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_nombre').value = this.getAttribute('data-nombre');
            document.getElementById('edit_tipo').value = this.getAttribute('data-tipo');
            document.getElementById('edit_desc_corta').value = this.getAttribute('data-desc-corta');
            
            const precio = this.getAttribute('data-precio');
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_precio_actual_db').value = precio; // Guardamos el precio original para comparar en el backend
        });
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
<?php
// modules/admin/catalogo_action.php
session_start();
require_once '../../config/database.php';

// Verificamos seguridad (Solo entra el Administrador por método POST)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$accion = $_POST['accion'] ?? '';

// ==========================================
// BLOQUE 1: CREAR NUEVO ÍTEM
// ==========================================
if ($accion === 'crear') {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $desc_corta = $_POST['desc_corta'];
    $desc_larga = $_POST['desc_larga'];
    $precio = $_POST['precio'];

    try {
        $db->beginTransaction();

        $query_cat = "INSERT INTO catalogo (nombre, tipo, desc_corta, desc_larga) VALUES (:nombre, :tipo, :desc_corta, :desc_larga)";
        $stmt_cat = $db->prepare($query_cat);
        $stmt_cat->execute([
            ':nombre' => $nombre,
            ':tipo' => $tipo,
            ':desc_corta' => $desc_corta,
            ':desc_larga' => $desc_larga
        ]);

        $catalogo_id = $db->lastInsertId();

        $query_precio = "INSERT INTO catalogo_precios (catalogo_id, precio) VALUES (:catalogo_id, :precio)";
        $stmt_precio = $db->prepare($query_precio);
        $stmt_precio->execute([
            ':catalogo_id' => $catalogo_id,
            ':precio' => $precio
        ]);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Error al guardar: " . $e->getMessage());
    }

    header('Location: catalogo.php');
    exit;
}

// ==========================================
// BLOQUE 2: ACTIVAR / DESACTIVAR (SOFT DELETE)
// ==========================================
elseif ($accion === 'toggle_estado') {
    $id = $_POST['id'];
    $nuevo_estado = ($_POST['estado_actual'] === 'Activo') ? 'Inactivo' : 'Activo';
    
    try {
        $query = "UPDATE catalogo SET estado = :estado WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
    } catch (Exception $e) {
        die("Error al cambiar estado: " . $e->getMessage());
    }
    
    header('Location: catalogo.php');
    exit;
}

// ==========================================
// BLOQUE 3: EDITAR ÍTEM Y ACTUALIZAR PRECIO
// ==========================================
elseif ($accion === 'editar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $desc_corta = $_POST['desc_corta'];
    $precio_nuevo = $_POST['precio_nuevo'];
    $precio_actual_db = $_POST['precio_actual_db'];

    try {
        $db->beginTransaction();

        // 1. Actualizar datos base
        $query_update = "UPDATE catalogo SET nombre = :nombre, tipo = :tipo, desc_corta = :desc_corta WHERE id = :id";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([
            ':nombre' => $nombre,
            ':tipo' => $tipo,
            ':desc_corta' => $desc_corta,
            ':id' => $id
        ]);

        // 2. Histórico de precios (Solo si cambió el valor) [cite: 47-51]
        if ($precio_nuevo != $precio_actual_db) {
            $query_precio = "INSERT INTO catalogo_precios (catalogo_id, precio) VALUES (:catalogo_id, :precio)";
            $stmt_precio = $db->prepare($query_precio);
            $stmt_precio->execute([
                ':catalogo_id' => $id,
                ':precio' => $precio_nuevo
            ]);
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Error al editar: " . $e->getMessage());
    }

    header('Location: catalogo.php');
    exit;
}

// ==========================================
// SEGURIDAD: SI LA ACCIÓN NO EXISTE
// ==========================================
else {
    header('Location: catalogo.php');
    exit;
}
?>
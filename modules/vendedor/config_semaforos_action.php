<?php
// modules/vendedor/config_semaforos_action.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Vendedor' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$datos_metas = $_POST['meta']; 
$vendedor_id = 2; // Simulado

try {
    $db->beginTransaction();

    // Borramos SOLO la configuración de este vendedor
    $stmt_delete = $db->prepare("DELETE FROM metas_vendedor WHERE vendedor_id = :id");
    $stmt_delete->execute([':id' => $vendedor_id]);

    $query = "INSERT INTO metas_vendedor (vendedor_id, etapa, rango_verde_min, rango_amarillo_min, rango_rojo_max) 
              VALUES (:vendedor_id, :etapa, :verde, :amarillo, :rojo)";
    $stmt = $db->prepare($query);

    foreach ($datos_metas as $etapa => $valores) {
        $stmt->execute([
            ':vendedor_id' => $vendedor_id,
            ':etapa' => $etapa,
            ':verde' => $valores['verde'],
            ':amarillo' => $valores['amarillo'],
            ':rojo' => $valores['rojo']
        ]);
    }

    $db->commit();
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Error al guardar tus metas: " . $e->getMessage());
}

header('Location: config_semaforos.php');
exit;
?>
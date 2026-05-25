<?php
// modules/admin/metas_action.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$datos_metas = $_POST['meta']; 

try {
    $db->beginTransaction();

    // LA MAGIA ESTÁ AQUÍ: Usamos DELETE en lugar de TRUNCATE
    // El DELETE respeta la transacción y permite el RollBack si algo sale mal.
    $db->exec("DELETE FROM metas_corporativas");

    $query = "INSERT INTO metas_corporativas (etapa, meta_diaria, rango_verde_min, rango_amarillo_min, rango_rojo_max) 
              VALUES (:etapa, :meta_diaria, :verde, :amarillo, :rojo)";
    $stmt = $db->prepare($query);

    foreach ($datos_metas as $etapa => $valores) {
        $stmt->execute([
            ':etapa' => $etapa,
            ':meta_diaria' => $valores['diaria'],
            ':verde' => $valores['verde'],
            ':amarillo' => $valores['amarillo'],
            ':rojo' => $valores['rojo']
        ]);
    }

    $db->commit();
} catch (Exception $e) {
    // Verificamos si la transacción sigue activa antes de intentar revertirla
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Error al guardar las metas: " . $e->getMessage());
}

header('Location: metas.php');
exit;
?>
<?php
// modules/vendedor/prospectos_action.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Vendedor' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$accion = $_POST['accion'] ?? '';
$vendedor_id = 2; // Simulado

if ($accion === 'crear_etapa_1') {
    $nombre = trim($_POST['nombre']);
    $comuna = trim($_POST['comuna']);
    $telefono = trim($_POST['telefono']);

    try {
        $query = "INSERT INTO prospectos (vendedor_id, etapa, nombre, comuna, telefono) 
                  VALUES (:vendedor_id, 1, :nombre, :comuna, :telefono)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':vendedor_id' => $vendedor_id,
            ':nombre' => $nombre,
            ':comuna' => $comuna,
            ':telefono' => $telefono
        ]);
        
    } catch (Exception $e) {
        die("Error al registrar prospecto: " . $e->getMessage());
    }

    header('Location: prospectos.php');
    exit;
}
?>
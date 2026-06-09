<?php
// login_action.php
session_start();
require_once 'config/database.php';

$rut_raw = $_POST['rut'] ?? '';
$password = $_POST['password'] ?? '';

// 1. EL LIMPIADOR: Quita puntos, guiones, espacios y deja la 'k' minúscula si la hay
$rut_limpio = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rut_raw));

if (empty($rut_limpio) || empty($password)) {
    header('Location: index.php?error=vacios');
    exit;
}

try {
    $db = (new Database())->getConnection();
    
    // Buscamos por la columna RUT
    $stmt = $db->prepare("SELECT id, rut, password, nombre_completo, rol, estado FROM usuarios WHERE rut = :rut LIMIT 1");
    $stmt->execute([':rut' => $rut_limpio]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        
        if ($user['estado'] !== 'Activo') {
            header('Location: index.php?error=inactivo');
            exit;
        }

        $_SESSION['id'] = $user['id'];
        $_SESSION['rut'] = $user['rut'];
        $_SESSION['nombre'] = $user['nombre_completo'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] === 'Administrador') {
            header('Location: modules/admin/catalogo.php'); 
        } else {
            header('Location: modules/vendedor/prospectos.php');
        }
        exit;

    } else {
        header('Location: index.php?error=credenciales');
        exit;
    }

} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
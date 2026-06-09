<?php
// modules/vendedor/embudo_action.php
session_start();
require_once '../../config/database.php';

// Seguridad estricta
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Vendedor' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DWYM-php/index.php');
    exit;
}

$db = (new Database())->getConnection();
$accion = $_POST['accion'] ?? '';
$vendedor_id = 2; // Simulado para Juan Vendedor

// ==========================================
// DE ETAPA 1 A ETAPA 2 (Agendar Cita)
// ==========================================
if ($accion === 'avanzar_etapa_2') {
    $prospecto_id = filter_var($_POST['prospecto_id'] ?? '', FILTER_VALIDATE_INT);
        $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_VALIDATE_EMAIL);
        $fecha_hora_cita = trim($_POST['fecha_hora_cita'] ?? '');

        if (!$prospecto_id || empty($correo) || empty($fecha_hora_cita)) {
            die("Error de validación: Faltan datos o el correo no es válido.");
        }

    if (!$prospecto_id || !$correo || empty($fecha_hora_cita)) {
        die("Error de validación de seguridad.");
    }

    try {
        $db->beginTransaction();

        $query_update = "UPDATE prospectos SET correo = :correo, etapa = 2 WHERE id = :id";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([':correo' => $correo, ':id' => $prospecto_id]);

        // Simulación de Google Calendar API (HU07)
        $google_event_id = "cal_event_" . uniqid(); 

        $query_cita = "INSERT INTO citas (prospecto_id, fecha_hora, google_event_id, estado)
                       VALUES (:prospecto_id, :fecha_hora, :google_event_id, 'Pendiente')";
        $stmt_cita = $db->prepare($query_cita);
        $stmt_cita->execute([
            ':prospecto_id' => $prospecto_id,
            ':fecha_hora' => $fecha_hora_cita,
            ':google_event_id' => $google_event_id
        ]);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Error: " . $e->getMessage());
    }

    header('Location: gestion_embudo.php?id=' . $prospecto_id);
    exit;
}

// ==========================================
// DE ETAPA 2 A ETAPA 3 (Cita Realizada)
// ==========================================
elseif ($accion === 'avanzar_etapa_3') {
    $prospecto_id = filter_var($_POST['prospecto_id'] ?? '', FILTER_VALIDATE_INT);
        $rut = trim($_POST['rut'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $fecha_nac = trim($_POST['fecha_nacimiento'] ?? '');

        if (!$prospecto_id || empty($rut) || empty($genero) || empty($fecha_nac)) {
            die("Error de validación: Datos de la cita incompletos.");
        }

    try {
        $db->beginTransaction();

        $query_update = "UPDATE prospectos SET rut = :rut, genero = :genero, fecha_nacimiento = :fecha_nac, etapa = 3 WHERE id = :id";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([
            ':rut' => $rut,
            ':genero' => $genero,
            ':fecha_nac' => $fecha_nac,
            ':id' => $prospecto_id
        ]);

        $query_cita = "UPDATE citas SET estado = 'Realizada' WHERE prospecto_id = :id AND estado = 'Pendiente'";
        $stmt_cita = $db->prepare($query_cita);
        $stmt_cita->execute([':id' => $prospecto_id]);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Error: " . $e->getMessage());
    }

    header('Location: gestion_embudo.php?id=' . $prospecto_id);
    exit;
}

// ==========================================
// DE ETAPA 3 A ETAPA 4 (Cerrar Venta y Referidos)
// ==========================================
elseif ($accion === 'cerrar_venta') {
    $prospecto_id = filter_var($_POST['prospecto_id'] ?? '', FILTER_VALIDATE_INT);
        $catalogo_id = filter_var($_POST['catalogo_id'] ?? '', FILTER_VALIDATE_INT);

    if (!$prospecto_id || !$catalogo_id) {
        die("Faltan datos de la venta.");
    }

    // --- MAGIA: SUBIDA DE ARCHIVO (Documento o Carnet) ---
    $archivo = $_FILES['documento'];
    $ruta_destino = '../../assets/uploads/';
    
    // Si la carpeta uploads no existe, PHP la crea automáticamente
    if (!file_exists($ruta_destino)) {
        mkdir($ruta_destino, 0777, true);
    }
    
    // Renombramos el archivo con time() para que no se sobrescriban si se llaman igual
    $nombre_archivo = time() . '_' . basename($archivo['name']);
    $ruta_final = $ruta_destino . $nombre_archivo;
    
    // Movemos el archivo temporal al servidor real
    if (!move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        die("Error crítico al subir el documento adjunto.");
    }
    
    // Esta es la ruta que guardaremos en MySQL
    $ruta_bd = 'assets/uploads/' . $nombre_archivo;

    try {
        $db->beginTransaction();

        // 1. Obtenemos el precio exacto actual del ítem para congelarlo 
        $stmt_precio = $db->prepare("SELECT precio FROM catalogo_precios WHERE catalogo_id = :id ORDER BY id DESC LIMIT 1");
        $stmt_precio->execute([':id' => $catalogo_id]);
        $precio_congelado = $stmt_precio->fetchColumn();

        // 2. Insertamos el contrato en la tabla ventas [cite: 21-23]
        $query_venta = "INSERT INTO ventas (prospecto_id, catalogo_id, precio_congelado, documento_ruta) 
                        VALUES (:prospecto_id, :catalogo_id, :precio_congelado, :documento_ruta)";
        $stmt_venta = $db->prepare($query_venta);
        $stmt_venta->execute([
            ':prospecto_id' => $prospecto_id,
            ':catalogo_id' => $catalogo_id,
            ':precio_congelado' => $precio_congelado,
            ':documento_ruta' => $ruta_bd
        ]);

        // 3. Avanzamos el cliente a la Etapa Final (4)
        $db->prepare("UPDATE prospectos SET etapa = 4 WHERE id = :id")->execute([':id' => $prospecto_id]);

        // 4. Ciclo Infinito de Ventas: Guardamos a los 3 referidos 
       // Aseguramos que se reciban como Arrays para evitar errores si vienen vacíos
        $ref_nombres = $_POST['ref_nombre'] ?? [];
        $ref_comunas = $_POST['ref_comuna'] ?? [];
        $ref_telefonos = $_POST['ref_telefono'] ?? [];

        $query_ref = "INSERT INTO prospectos (vendedor_id, etapa, nombre, comuna, telefono) VALUES (:vendedor_id, 1, :nombre, :comuna, :telefono)";
        $stmt_ref = $db->prepare($query_ref);

        // Recorremos los arrays de referidos
        for ($i = 0; $i < 3; $i++) {
            if (!empty($ref_nombres[$i])) {
                $stmt_ref->execute([
                    ':vendedor_id' => $vendedor_id,
                    ':nombre' => $ref_nombres[$i],
                    ':comuna' => $ref_comunas[$i],
                    ':telefono' => $ref_telefonos[$i]
                ]);
            }
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Error al cerrar la venta: " . $e->getMessage());
    }

    header('Location: gestion_embudo.php?id=' . $prospecto_id);
    exit;
}
?>
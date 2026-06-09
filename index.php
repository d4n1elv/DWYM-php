<?php
// index.php
session_start();
if (isset($_SESSION['rol'])) {
    // Si ya tiene sesión, lo pateamos a su dashboard
    $destino = ($_SESSION['rol'] === 'Administrador') ? 'modules/admin/catalogo.php' : 'modules/vendedor/prospectos.php';
    header('Location: ' . $destino);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRM Funeraria UDP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .login-header { background-color: #63a388; color: white; border-radius: 10px 10px 0 0; padding: 20px; text-align: center; }
        .btn-custom { background-color: #63a388; border: none; color: white; }
        .btn-custom:hover { background-color: #4b826a; color: white; }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="login-header">
        <h4><i class="bi bi-shield-lock-fill me-2"></i>CRM Funeraria UDP</h4>
        <p class="mb-0 text-white-50">Acceso a Sistema Interno</p>
    </div>
    <div class="card-body p-4">
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2 text-center">
                <?php 
                    if($_GET['error'] === 'vacios') echo "Ingresa tus credenciales.";
                    elseif($_GET['error'] === 'credenciales') echo "Usuario o contraseña incorrectos.";
                    elseif($_GET['error'] === 'inactivo') echo "Cuenta suspendida.";
                ?>
            </div>
        <?php endif; ?>

        <form action="login_action.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">RUT</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" class="form-control" name="rut" placeholder="Ej: 11.111.111-9" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #273752; border:none;">Ingresar al CRM</button>
        </form>
    </div>
</div>

</body>
</html>
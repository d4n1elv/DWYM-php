<?php
// index.php - Login Simulado

// 1. INICIAR SESIÓN: Esto es vital en PHP. Le dice al servidor que 
// prepare un espacio en memoria para recordar datos del usuario mientras navega.
session_start();

// 2. Procesar el formulario cuando el usuario haga clic en "Ingresar"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol_seleccionado = $_POST['rol'];
    
    // Guardamos el rol en la variable global $_SESSION
    $_SESSION['rol'] = $rol_seleccionado;
    
    // Simulamos los datos del usuario dependiendo del rol
    if ($rol_seleccionado === 'Administrador') {
        $_SESSION['nombre_usuario'] = 'Admin Principal';
        // Redirigimos al panel del administrador (que crearemos después)
        header('Location: modules/admin/catalogo.php');
        exit;
    } else {
        $_SESSION['nombre_usuario'] = 'Juan Vendedor';
        // Redirigimos al panel del vendedor (que crearemos después)
        header('Location: modules/vendedor/dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRM Funeraria</title>
    <link rel="stylesheet" href="./assets/css/adminlte.css" />
    <style>
        /* Un poco de CSS personalizado para centrar el login en la pantalla */
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f6f9; /* Color de fondo típico de AdminLTE */
        }
        .login-box {
            width: 400px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="login-box text-center">
        <h2 class="mb-4"><b>CRM</b> Ventas</h2>
        <p class="text-muted">Simulador de Ingreso</p>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="rol" class="form-label">Selecciona tu Perfil:</label>
                <select name="rol" id="rol" class="form-select form-select-lg" required>
                    <option value="" disabled selected>-- Elige un rol --</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Vendedor">Vendedor</option>
                </select>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Ingresar al Sistema</button>
            </div>
        </form>
    </div>

</body>
</html>
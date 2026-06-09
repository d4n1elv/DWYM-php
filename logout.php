<?php
// logout.php
session_start(); // Retomamos la sesión actual
session_unset(); // Vaciamos todas las variables de memoria
session_destroy(); // Destruimos la sesión por completo

// Redirigimos al usuario de vuelta al login con un mensaje de éxito (opcional)
header("Location: /DWYM-php/index.php");
exit;
?>
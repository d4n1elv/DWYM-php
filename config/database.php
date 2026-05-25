<?php
// config/database.php

/**
 * Clase Database
 * Utilizamos el patrón de diseño Singleton básico (o simplemente una clase envoltorio)
 * para manejar la conexión a la base de datos usando PDO (PHP Data Objects).
 * PDO es el estándar actual porque es más seguro contra Inyecciones SQL.
 */
class Database {
    // Credenciales por defecto de XAMPP
    private $host = "localhost";
    private $db_name = "crm_ventas_db";
    private $username = "root"; // Usuario por defecto en XAMPP
    private $password = "";     // Contraseña por defecto en XAMPP (vacía)
    public $conn;

    /**
     * Método para obtener la conexión a la base de datos
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Intentamos crear una nueva conexión PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password
            );
            
            // Configuramos PDO para que lance excepciones si ocurre un error
            // Esto nos ayuda a detectar problemas rápidamente durante el desarrollo
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $exception) {
            // Si las credenciales fallan o la base de datos no existe, atrapamos el error aquí
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
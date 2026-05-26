<?php
class Database {
    private $host = 'db';
    private $db_name = 'citas_medicas_db';
    private $username = 'root';
    private $password = 'root'; // Por defecto en Laragon es vacío
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Configurar caracteres UTF8 para tildes y ñ
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
        return $this->conn;
    }
}

<?php
class Auditoria {
    private $conn;
    private $table = 'auditoria';

    public function __construct($db) {
        $this->conn = $db;
    }

    // LISTAR LOGS (Para el reporte del admin)
    public function leer() {
        $query = "SELECT a.*, u.nombre as usuario, u.rol_nombre_temp 
                  FROM (SELECT users.*, roles.nombre as rol_nombre_temp FROM usuarios users JOIN roles ON users.id_rol = roles.id_rol) u
                  JOIN " . $this->table . " a ON a.id_usuario = u.id_usuario 
                  ORDER BY a.fecha_hora DESC";
        
        // Nota: Hacemos una subconsulta o join simple dependiendo de tu estructura exacta, 
        // pero esto debería funcionar con la estructura actual.
        // Mejor consulta directa:
        $query = "SELECT a.*, u.nombre as usuario 
                  FROM " . $this->table . " a
                  JOIN usuarios u ON a.id_usuario = u.id_usuario
                  ORDER BY a.fecha_hora DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // REGISTRAR EVENTO
    public function registrar($id_usuario, $accion, $tabla, $id_afectado, $descripcion) {
        $query = "INSERT INTO " . $this->table . " (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion, ip_usuario) 
                  VALUES (:user, :accion, :tabla, :id_af, :desc, :ip)";
        
        $stmt = $this->conn->prepare($query);
        
        // Obtener IP del cliente
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt->bindParam(':user', $id_usuario);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':tabla', $tabla);
        $stmt->bindParam(':id_af', $id_afectado);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':ip', $ip);

        return $stmt->execute();
    }
}
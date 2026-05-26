<?php
class Servicio {
    private $conn;
    private $table = 'servicios';

    public function __construct($db) {
        $this->conn = $db;
    }

    // LISTAR TODOS
    public function leer() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre_servicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // LISTAR SOLO ACTIVOS (Para llenar selects en citas)
    public function leerActivos() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 'Activo' ORDER BY nombre_servicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // CREAR
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " (nombre_servicio, descripcion, precio) VALUES (:nombre, :desc, :precio)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':desc', $datos['descripcion']);
        $stmt->bindParam(':precio', $datos['precio']);
        return $stmt->execute();
    }

    // ACTUALIZAR
    public function actualizar($datos) {
        $query = "UPDATE " . $this->table . " SET nombre_servicio = :nombre, descripcion = :desc, precio = :precio, estado = :estado WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $datos['id']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':desc', $datos['descripcion']);
        $stmt->bindParam(':precio', $datos['precio']);
        $stmt->bindParam(':estado', $datos['estado']);
        return $stmt->execute();
    }

    // ELIMINAR (Soft delete recomendado, pero usaremos físico por ahora)
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
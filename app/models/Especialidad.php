<?php
class Especialidad {
    private $conn;
    private $table = 'especialidades';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LEER (Incluye estado y conteo de médicos)
    public function leer() {
        $query = 'SELECT e.id_especialidad, e.nombre, e.estado,
                         (SELECT COUNT(*) FROM medicos m WHERE m.id_especialidad = e.id_especialidad) as total_medicos 
                  FROM ' . $this->table . ' e
                  ORDER BY e.nombre ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. CREAR (Estado por defecto 1 = Activo)
    public function crear($nombre) {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, estado) VALUES (:nombre, 1)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        return $stmt->execute();
    }

    // 3. ACTUALIZAR
    public function actualizar($id, $nombre) {
        $query = 'UPDATE ' . $this->table . ' SET nombre = :nombre WHERE id_especialidad = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 4. CAMBIAR ESTADO (PARA ACTIVAR/DESACTIVAR)
    public function cambiarEstado($id, $nuevoEstado) {
        $query = 'UPDATE ' . $this->table . ' SET estado = :estado WHERE id_especialidad = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 5. ELIMINAR (Físico - Opcional)
    public function eliminar($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id_especialidad = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
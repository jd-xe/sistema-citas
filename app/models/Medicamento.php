<?php
class Medicamento {
    private $conn;
    private $table = 'medicamentos';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LISTAR
    public function leer() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre_comercial ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. CREAR
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " (nombre_comercial, nombre_generico, presentacion, stock) 
                  VALUES (:comercial, :generico, :presentacion, :stock)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comercial', $datos['nombre_comercial']);
        $stmt->bindParam(':generico', $datos['nombre_generico']);
        $stmt->bindParam(':presentacion', $datos['presentacion']);
        $stmt->bindParam(':stock', $datos['stock']);
        return $stmt->execute();
    }

    // 3. ACTUALIZAR
    public function actualizar($datos) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre_comercial = :comercial, 
                      nombre_generico = :generico, 
                      presentacion = :presentacion, 
                      stock = :stock,
                      estado = :estado
                  WHERE id_medicamento = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $datos['id']);
        $stmt->bindParam(':comercial', $datos['nombre_comercial']);
        $stmt->bindParam(':generico', $datos['nombre_generico']);
        $stmt->bindParam(':presentacion', $datos['presentacion']);
        $stmt->bindParam(':stock', $datos['stock']);
        $stmt->bindParam(':estado', $datos['estado']);
        return $stmt->execute();
    }

    // 4. ELIMINAR
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_medicamento = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
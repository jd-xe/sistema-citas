<?php
class Configuracion {
    private $conn;
    private $table = 'configuracion';

    public function __construct($db) {
        $this->conn = $db;
    }

    // OBTENER DATOS
    public function obtener() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ACTUALIZAR DATOS (Incluyendo Logo)
    public function actualizar($datos) {
        // Construimos la consulta base
        $query = "UPDATE " . $this->table . " 
                  SET nombre_clinica = :nombre, 
                      direccion = :direccion, 
                      telefono = :telefono, 
                      email = :email, 
                      moneda = :moneda";
        
        // Si se subió un logo nuevo, agregamos ese campo a la actualización
        if (!empty($datos['logo'])) {
            $query .= ", logo = :logo";
        }

        $query .= " WHERE id = 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':moneda', $datos['moneda']);

        if (!empty($datos['logo'])) {
            $stmt->bindParam(':logo', $datos['logo']);
        }

        return $stmt->execute();
    }
}
<?php
class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LOGIN: Buscar por Email
    public function getByEmail($email) {
        $query = 'SELECT u.id_usuario, u.nombre, u.email, u.password, u.id_rol, u.avatar, r.nombre as rol_nombre 
                  FROM ' . $this->table . ' u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.email = :email LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. OBTENER POR ID (Incluye Avatar)
    public function getById($id) {
        $query = 'SELECT u.id_usuario, u.nombre, u.email, u.password, u.avatar, r.nombre as rol_nombre 
                  FROM ' . $this->table . ' u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.id_usuario = :id LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. ACTUALIZAR PERFIL (Con Foto)
    public function actualizar($id, $nombre, $password = null, $avatar = null) {
        // Construcción dinámica de la consulta
        $query = "UPDATE " . $this->table . " SET nombre = :nombre";
        
        if ($password) {
            $query .= ", password = :password";
        }
        if ($avatar) {
            $query .= ", avatar = :avatar";
        }

        $query .= " WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id', $id);

        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hash);
        }
        if ($avatar) {
            $stmt->bindParam(':avatar', $avatar);
        }

        return $stmt->execute();
    }
}
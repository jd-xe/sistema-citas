<?php
class Paciente {
    private $conn;
    private $table = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function leer($id_medico = null) {
        if ($id_medico) {
            // Solo trae pacientes que tengan al menos una cita con este médico
            $query = "SELECT DISTINCT u.* FROM " . $this->table . " u 
                    JOIN citas c ON u.id_usuario = c.id_paciente 
                    WHERE u.id_rol = 3 AND c.id_medico = :id_medico 
                    ORDER BY u.nombre ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_medico', $id_medico);
        } else {
            // Trae a todos los pacientes (Comportamiento normal para Admin o Recepción)
            $query = "SELECT * FROM " . $this->table . " WHERE id_rol = 3 ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt;
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id AND id_rol = 3";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function contarTotal() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE id_rol = 3 AND estado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " (nombre, email, password, telefono, documento_identidad, grupo_sanguineo, alergias, enfermedades_cronicas, id_rol, estado) VALUES (:nombre, :email, :password, :telefono, :doc, :grupo, :alergias, :cronicas, 3, 1)";
        $stmt = $this->conn->prepare($query);
        
        // Lógica de Contraseña:
        // Si el usuario envió una contraseña, úsala. Si no, usa el DNI.
        $plainPassword = !empty($datos['password']) ? $datos['password'] : $datos['documento_identidad'];
        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':doc', $datos['documento_identidad']);
        $stmt->bindParam(':grupo', $datos['grupo_sanguineo']);
        $stmt->bindParam(':alergias', $datos['alergias']);
        $stmt->bindParam(':cronicas', $datos['enfermedades_cronicas']);
        
        return $stmt->execute();
    }

    public function actualizar($datos) {
        // Query base para datos personales
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      email = :email, 
                      telefono = :telefono, 
                      documento_identidad = :doc,
                      grupo_sanguineo = :grupo,
                      alergias = :alergias,
                      enfermedades_cronicas = :cronicas";
        
        // Si hay password, la agregamos a la consulta
        if (!empty($datos['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id_usuario = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':doc', $datos['documento_identidad']);
        $stmt->bindParam(':grupo', $datos['grupo_sanguineo']);
        $stmt->bindParam(':alergias', $datos['alergias']);
        $stmt->bindParam(':cronicas', $datos['enfermedades_cronicas']);
        $stmt->bindParam(':id', $datos['id_usuario']);
        
        // Hasheamos y bindeamos solo si existe password nueva
        if (!empty($datos['password'])) {
            $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $passHash);
        }
        
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado) {
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
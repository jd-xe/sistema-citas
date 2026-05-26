<?php
class Medico {
    private $conn;
    private $table = 'medicos';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LEER
    public function leer() {
        $query = 'SELECT m.id_medico, m.colegiatura, m.id_especialidad,
                         u.id_usuario, u.nombre, u.email, u.telefono, u.estado,
                         e.nombre as especialidad
                  FROM ' . $this->table . ' m
                  JOIN usuarios u ON m.id_usuario = u.id_usuario
                  JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                  ORDER BY u.nombre ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. OBTENER POR ID
    public function obtenerPorId($id) {
        $query = "SELECT m.id_medico, m.colegiatura, u.nombre, u.email, e.nombre as especialidad 
                  FROM " . $this->table . " m 
                  JOIN usuarios u ON m.id_usuario = u.id_usuario 
                  JOIN especialidades e ON m.id_especialidad = e.id_especialidad 
                  WHERE m.id_medico = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. VERIFICAR HORARIO LABORAL (NUEVA FUNCIÓN - SOLUCIÓN AL ERROR)
    public function verificaHorarioLaboral($id_medico, $fecha_hora) {
        // Convertir fecha a día de la semana en español
        $dias = [
            'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'
        ];
        
        $timestamp = strtotime($fecha_hora);
        $dia_ingles = date('l', $timestamp);
        $dia_es = $dias[$dia_ingles] ?? '';
        $hora_cita = date('H:i:s', $timestamp);

        // Consulta: ¿Existe un horario ese día que cubra la hora de la cita?
        $sql = "SELECT COUNT(*) FROM horarios_medicos 
                WHERE id_medico = :id 
                AND dia_semana = :dia 
                AND :hora >= hora_inicio 
                AND :hora < hora_fin";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_medico);
        $stmt->bindParam(':dia', $dia_es);
        $stmt->bindParam(':hora', $hora_cita);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    // 4. CREAR
    public function crear($datos) {
        try {
            $this->conn->beginTransaction();
            $queryUser = "INSERT INTO usuarios (nombre, email, password, id_rol, estado) VALUES (:nombre, :email, :password, 2, 1)";
            $stmtUser = $this->conn->prepare($queryUser);
            $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);
            $stmtUser->bindParam(':nombre', $datos['nombre']);
            $stmtUser->bindParam(':email', $datos['email']);
            $stmtUser->bindParam(':password', $passHash);
            $stmtUser->execute();
            $id_usuario = $this->conn->lastInsertId();
            $queryMedico = "INSERT INTO medicos (id_usuario, id_especialidad, colegiatura) VALUES (:id_usuario, :id_especialidad, :colegiatura)";
            $stmtMedico = $this->conn->prepare($queryMedico);
            $stmtMedico->bindParam(':id_usuario', $id_usuario);
            $stmtMedico->bindParam(':id_especialidad', $datos['id_especialidad']);
            $stmtMedico->bindParam(':colegiatura', $datos['colegiatura']);
            $stmtMedico->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 5. ACTUALIZAR
    public function actualizar($datos) {
        try {
            $this->conn->beginTransaction();
            $sqlUser = "UPDATE usuarios SET nombre = :nombre, email = :email";
            if (!empty($datos['password'])) { $sqlUser .= ", password = :password"; }
            $sqlUser .= " WHERE id_usuario = :id_usuario";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bindParam(':nombre', $datos['nombre']);
            $stmtUser->bindParam(':email', $datos['email']);
            $stmtUser->bindParam(':id_usuario', $datos['id_usuario']);
            if (!empty($datos['password'])) {
                $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);
                $stmtUser->bindParam(':password', $passHash);
            }
            $stmtUser->execute();
            $sqlMedico = "UPDATE medicos SET id_especialidad = :id_especialidad, colegiatura = :colegiatura WHERE id_medico = :id_medico";
            $stmtMedico = $this->conn->prepare($sqlMedico);
            $stmtMedico->bindParam(':id_especialidad', $datos['id_especialidad']);
            $stmtMedico->bindParam(':colegiatura', $datos['colegiatura']);
            $stmtMedico->bindParam(':id_medico', $datos['id_medico']);
            $stmtMedico->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 6. CAMBIAR ESTADO
    public function cambiarEstado($id_usuario, $nuevoEstado) {
        $query = 'UPDATE usuarios SET estado = :estado WHERE id_usuario = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id_usuario);
        return $stmt->execute();
    }

    // --- HORARIOS ---
    public function obtenerHorarios($id_medico) {
        $query = "SELECT * FROM horarios_medicos WHERE id_medico = :id_medico ORDER BY dia_semana, hora_inicio";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_medico', $id_medico);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarHorario($id_medico, $dia, $inicio, $fin) {
        $query = "INSERT INTO horarios_medicos (id_medico, dia_semana, hora_inicio, hora_fin) VALUES (:id, :dia, :inicio, :fin)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_medico); $stmt->bindParam(':dia', $dia); $stmt->bindParam(':inicio', $inicio); $stmt->bindParam(':fin', $fin);
        return $stmt->execute();
    }

    public function eliminarHorario($id_horario) {
        $query = "DELETE FROM horarios_medicos WHERE id_horario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_horario);
        return $stmt->execute();
    }
}
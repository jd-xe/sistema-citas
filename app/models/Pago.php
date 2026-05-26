<?php
class Pago {
    private $conn;
    private $table = 'pagos';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. REGISTRAR UN PAGO (Ya lo tenías, lo mantenemos)
    public function registrar($datos) {
        $query = "INSERT INTO " . $this->table . " (id_cita, monto, metodo_pago, observaciones) 
                  VALUES (:cita, :monto, :metodo, :obs)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cita', $datos['id_cita']);
        $stmt->bindParam(':monto', $datos['monto']);
        $stmt->bindParam(':metodo', $datos['metodo_pago']);
        $stmt->bindParam(':obs', $datos['observaciones']);

        // Al registrar pago, actualizamos la cita para vincularla (aunque ya lo hacemos vía ID, esto asegura integridad)
        // Nota: En este diseño simple, el ID de pago se genera auto, la relación es Cita -> Pago o Pago -> Cita.
        // En tu DB tienes id_cita en la tabla pagos, eso es suficiente.
        
        return $stmt->execute();
    }

    // 2. NUEVO: LISTAR PAGOS (Historial de Caja)
    public function listar($inicio = null, $fin = null) {
        $query = "SELECT p.id_pago, p.monto, p.metodo_pago, p.fecha_pago, p.observaciones,
                         u.nombre as paciente, 
                         s.nombre_servicio
                  FROM " . $this->table . " p
                  JOIN citas c ON p.id_cita = c.id_cita
                  JOIN usuarios u ON c.id_paciente = u.id_usuario
                  LEFT JOIN servicios s ON c.id_servicio = s.id_servicio";
        
        $condiciones = [];
        
        // Filtro de fechas (Por defecto hoy si no se envía nada, o rango específico)
        if ($inicio && $fin) {
            $condiciones[] = "DATE(p.fecha_pago) BETWEEN :inicio AND :fin";
        }

        if (count($condiciones) > 0) {
            $query .= " WHERE " . implode(' AND ', $condiciones);
        }

        $query .= " ORDER BY p.fecha_pago DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($inicio && $fin) {
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fin', $fin);
        }

        $stmt->execute();
        return $stmt;
    }

    // 3. NUEVO: ELIMINAR PAGO (Anular cobro)
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_pago = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
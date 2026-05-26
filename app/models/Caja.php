<?php
class Caja {
    private $conn;
    private $table_sesiones = 'sesiones_caja';
    private $table_gastos = 'gastos';
    private $table_pagos = 'pagos';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. REGISTRAR COBRO (INGRESO)
    public function registrarCobro($id_cita, $monto, $metodo_pago, $observaciones) {
        try {
            $this->conn->beginTransaction();
            $query = "INSERT INTO " . $this->table_pagos . " (id_cita, monto, metodo_pago, fecha_pago, observaciones) 
                      VALUES (:id_cita, :monto, :metodo, NOW(), :obs)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_cita', $id_cita);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':metodo', $metodo_pago);
            $stmt->bindParam(':obs', $observaciones);
            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- MÉTODOS DE CAJA ---
    public function obtenerSesionActiva($id_usuario) {
        $query = "SELECT * FROM " . $this->table_sesiones . " WHERE id_usuario = :id AND estado = 'abierta' LIMIT 1";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id_usuario); $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function abrirCaja($id_usuario, $monto) {
        $query = "INSERT INTO " . $this->table_sesiones . " (id_usuario, monto_apertura, fecha_apertura, estado) VALUES (:id, :monto, NOW(), 'abierta')";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id_usuario); $stmt->bindParam(':monto', $monto);
        return $stmt->execute();
    }

    public function registrarGasto($id_sesion, $descripcion, $monto) {
        $query = "INSERT INTO " . $this->table_gastos . " (id_sesion, descripcion, monto, fecha_gasto) VALUES (:id, :desc, :monto, NOW())";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id_sesion); $stmt->bindParam(':desc', $descripcion); $stmt->bindParam(':monto', $monto);
        return $stmt->execute();
    }

    public function obtenerBalance($id_sesion, $fecha_apertura) {
        // Ingresos
        $sqlIngresos = "SELECT SUM(monto) as total FROM " . $this->table_pagos . " WHERE fecha_pago >= :fecha";
        $stmtIng = $this->conn->prepare($sqlIngresos); $stmtIng->bindParam(':fecha', $fecha_apertura); $stmtIng->execute();
        $ingresos = $stmtIng->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        // Gastos
        $sqlGastos = "SELECT SUM(monto) as total FROM " . $this->table_gastos . " WHERE id_sesion = :id";
        $stmtGas = $this->conn->prepare($sqlGastos); $stmtGas->bindParam(':id', $id_sesion); $stmtGas->execute();
        $gastos = $stmtGas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        return ['ingresos' => $ingresos, 'gastos' => $gastos];
    }

    public function listarGastos($id_sesion) {
        $query = "SELECT * FROM " . $this->table_gastos . " WHERE id_sesion = :id ORDER BY fecha_gasto DESC";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id_sesion); $stmt->execute(); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarIngresosRecientes() {
        $query = "SELECT p.*, u.nombre as paciente, s.nombre_servicio 
                  FROM " . $this->table_pagos . " p
                  JOIN citas c ON p.id_cita = c.id_cita
                  JOIN usuarios u ON c.id_paciente = u.id_usuario
                  LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
                  ORDER BY p.fecha_pago DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cerrarCaja($id_sesion, $monto_cierre, $obs) {
        $query = "UPDATE " . $this->table_sesiones . " SET monto_cierre = :monto, fecha_cierre = NOW(), estado = 'cerrada', observaciones = :obs WHERE id_sesion = :id";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':monto', $monto_cierre); $stmt->bindParam(':obs', $obs); $stmt->bindParam(':id', $id_sesion);
        return $stmt->execute();
    }
}
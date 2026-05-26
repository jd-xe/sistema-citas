<?php
class Reporte {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Total Ingresos en un rango de fechas
    public function totalIngresos($inicio, $fin) {
        $sql = "SELECT IFNULL(SUM(monto), 0) as total 
                FROM pagos 
                WHERE DATE(fecha_pago) BETWEEN :inicio AND :fin";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // 2. Cantidad de Citas por Estado
    public function citasPorEstado($inicio, $fin) {
        $sql = "SELECT estado, COUNT(*) as cantidad 
                FROM citas 
                WHERE DATE(fecha_cita) BETWEEN :inicio AND :fin 
                GROUP BY estado";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Top Servicios Más Vendidos (Requiere JOINs)
    public function serviciosMasVendidos($inicio, $fin) {
        $sql = "SELECT s.nombre_servicio, COUNT(c.id_servicio) as cantidad, SUM(p.monto) as ingresos
                FROM citas c
                JOIN servicios s ON c.id_servicio = s.id_servicio
                JOIN pagos p ON c.id_cita = p.id_cita
                WHERE DATE(p.fecha_pago) BETWEEN :inicio AND :fin
                GROUP BY s.nombre_servicio
                ORDER BY ingresos DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Rendimiento por Médico (Quién atiende más)
    public function rendimientoMedicos($inicio, $fin) {
        $sql = "SELECT u.nombre as medico, COUNT(c.id_cita) as total_citas
                FROM citas c
                JOIN medicos m ON c.id_medico = m.id_medico
                JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE DATE(c.fecha_cita) BETWEEN :inicio AND :fin
                AND c.estado != 'Cancelada'
                GROUP BY u.nombre
                ORDER BY total_citas DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
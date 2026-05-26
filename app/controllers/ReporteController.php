<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Reporte.php';
require_once APP_ROOT . '/models/Configuracion.php';

class ReporteController {
    
    public function index() {
        // SEGURIDAD: Solo Admin
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $reporteModel = new Reporte($db);
        $configModel = new Configuracion($db);
        $empresa = $configModel->obtener();

        // Fechas por defecto: Del 1ro del mes hasta hoy
        $fechaInicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
        $fechaFin = isset($_GET['fin']) ? $_GET['fin'] : date('Y-m-d');

        // Obtener métricas
        $ingresos = $reporteModel->totalIngresos($fechaInicio, $fechaFin);
        $estadosCitas = $reporteModel->citasPorEstado($fechaInicio, $fechaFin);
        $topServicios = $reporteModel->serviciosMasVendidos($fechaInicio, $fechaFin);
        $topMedicos = $reporteModel->rendimientoMedicos($fechaInicio, $fechaFin);

        require_once APP_ROOT . '/views/admin/reportes.php';
    }
}
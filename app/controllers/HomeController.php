<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Medico.php';
require_once APP_ROOT . '/models/Paciente.php';

class HomeController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Si no está logueado, redirigir al login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $citaModel = new Cita($db);
        $medicoModel = new Medico($db);
        $pacienteModel = new Paciente($db);
        
        $rol = $_SESSION['user_role_id'];
        $userId = $_SESSION['user_id'];
        $medicoId = $_SESSION['medico_id'] ?? null; // Si es médico

        // 1. OBTENER ESTADÍSTICAS (CONTADORES)
        if ($rol == 2 && $medicoId) { // Médico
            $totalCitas = $citaModel->contarTotal($medicoId);
            $totalPacientes = $pacienteModel->contarTotal();
            $totalMedicos = 1;
            $statsEstado = $citaModel->obtenerEstadisticasEstado($medicoId);
        } elseif ($rol == 3) { // Paciente
            $totalCitas = $citaModel->contarTotal(null, $userId);
            $totalPacientes = 1;
            $totalMedicos = $medicoModel->leer()->rowCount();
            $statsEstado = $citaModel->obtenerEstadisticasEstado(null, $userId);
        } else { // Admin / Recepción
            $totalCitas = $citaModel->contarTotal();
            // Aquí es donde ocurría el error, ahora ya existe la función
            $totalPacientes = $pacienteModel->contarTotal();
            $totalMedicos = $medicoModel->leer()->rowCount();
            $statsEstado = $citaModel->obtenerEstadisticasEstado();
        }

        // 2. OBTENER LISTA DE PRÓXIMAS CITAS (INCLUYENDO FECHAS)
        $proximasCitas = $citaModel->obtenerProximasCitas(6);

        require_once APP_ROOT . '/views/admin/home.php';
    }
}
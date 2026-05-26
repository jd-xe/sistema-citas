<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Auditoria.php';

class AuditoriaController {
    
    public function index() {
        // SEGURIDAD: SOLO ADMIN
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $auditoriaModel = new Auditoria($db);
        $logs = $auditoriaModel->leer();
        
        require_once APP_ROOT . '/views/admin/auditoria.php';
    }
}
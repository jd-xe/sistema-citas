<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Sistema.php';

class SistemaController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Solo Admin (Rol 1) puede ver esto
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) { 
            header('Location: ' . BASE_URL . '/home'); 
            exit; 
        }

        require_once APP_ROOT . '/views/admin/sistema_reset.php';
    }

    public function ejecutarReset() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Seguridad: Solo Admin
        if ($_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/home'); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $userId = $_SESSION['user_id'];

            $database = new Database();
            $db = $database->connect();
            $sistemaModel = new Sistema($db);

            // 1. Verificamos la contraseña
            // Ahora el modelo usa fetchAll(), así que la conexión queda limpia inmediatamente
            if ($sistemaModel->verificarPasswordAdmin($userId, $password)) {
                
                // 2. Ejecutamos el restablecimiento en la misma conexión (ahora segura)
                if ($sistemaModel->restablecerSistema()) {
                    // Si éxito: Cerrar sesión y mandar al login
                    session_destroy();
                    header('Location: ' . BASE_URL . '/login?msg=reset_ok');
                } else {
                    // Si fallo SQL
                    header('Location: ' . BASE_URL . '/sistema?msg=error_db');
                }

            } else {
                // Si contraseña incorrecta
                header('Location: ' . BASE_URL . '/sistema?msg=error_pass');
            }
        }
    }
}
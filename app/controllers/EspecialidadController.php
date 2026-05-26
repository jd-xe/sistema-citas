<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Especialidad.php';

class EspecialidadController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Solo Admin (Rol 1)
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) { 
            header('Location: ' . BASE_URL . '/home'); 
            exit; 
        }

        $database = new Database();
        $db = $database->connect();
        $especialidadModel = new Especialidad($db);
        
        $resultado = $especialidadModel->leer();
        
        require_once APP_ROOT . '/views/admin/especialidades.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); 
            $db = $database->connect(); 
            $especialidadModel = new Especialidad($db);

            if($especialidadModel->crear($_POST['nombre'])) {
                header('Location: ' . BASE_URL . '/especialidades?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/especialidades?msg=error');
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); 
            $db = $database->connect(); 
            $especialidadModel = new Especialidad($db);

            if($especialidadModel->actualizar($_POST['id_especialidad'], $_POST['nombre'])) {
                header('Location: ' . BASE_URL . '/especialidades?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/especialidades?msg=error');
            }
        }
    }

    // --- FUNCIÓN ACTIVAR / DESACTIVAR ---
    public function cambiarEstado() {
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $database = new Database(); 
            $db = $database->connect(); 
            $especialidadModel = new Especialidad($db);
            
            $id = $_GET['id'];
            $estadoActual = intval($_GET['estado']);
            $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
            
            if ($especialidadModel->cambiarEstado($id, $nuevoEstado)) {
                $msg = ($nuevoEstado == 1) ? 'activado' : 'desactivado';
                header('Location: ' . BASE_URL . '/especialidades?msg=' . $msg);
            } else {
                header('Location: ' . BASE_URL . '/especialidades?msg=error');
            }
        } else {
            header('Location: ' . BASE_URL . '/especialidades');
        }
    }
}
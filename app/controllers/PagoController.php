<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Pago.php';
require_once APP_ROOT . '/models/Configuracion.php';

class PagoController {
    
    public function index() {
        // Seguridad: Admin (1) y Recepción (4)
        if (!isset($_SESSION['user_role_id']) || ($_SESSION['user_role_id'] != 1 && $_SESSION['user_role_id'] != 4)) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $pagoModel = new Pago($db);
        $configModel = new Configuracion($db);
        $empresa = $configModel->obtener();

        $fechaInicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
        $fechaFin = isset($_GET['fin']) ? $_GET['fin'] : date('Y-m-d');

        $resultado = $pagoModel->listar($fechaInicio, $fechaFin);
        
        require_once APP_ROOT . '/views/admin/pagos.php';
    }

    public function eliminar() {
        // SEGURIDAD CRÍTICA: SOLO ADMIN (Rol 1) PUEDE ELIMINAR PAGOS
        // La Recepcionista (Rol 4) NO puede entrar aquí.
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            // Si intenta borrar, lo mandamos fuera
            header('Location: ' . BASE_URL . '/pagos?msg=error_permisos'); 
            exit;
        }

        if (isset($_GET['id'])) {
            $database = new Database();
            $db = $database->connect();
            $pagoModel = new Pago($db);
            
            if ($pagoModel->eliminar($_GET['id'])) {
                header('Location: ' . BASE_URL . '/pagos?msg=eliminado');
            } else {
                header('Location: ' . BASE_URL . '/pagos?msg=error');
            }
        }
    }
}
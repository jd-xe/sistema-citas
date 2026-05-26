<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Servicio.php';
// Necesitamos configuración para saber la moneda
require_once APP_ROOT . '/models/Configuracion.php';

class ServicioController {
    
    public function index() {
        // SEGURIDAD: Solo Admin puede ver esto
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $servicioModel = new Servicio($db);
        $configModel = new Configuracion($db);
        
        $resultado = $servicioModel->leer();
        $empresa = $configModel->obtener(); // Para el símbolo de moneda
        
        require_once APP_ROOT . '/views/admin/servicios.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->connect();
            $servicioModel = new Servicio($db);

            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio']
            ];

            if($servicioModel->crear($datos)) {
                header('Location: ' . BASE_URL . '/servicios?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/servicios?msg=error');
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->connect();
            $servicioModel = new Servicio($db);

            $datos = [
                'id' => $_POST['id_servicio'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'estado' => $_POST['estado']
            ];

            if($servicioModel->actualizar($datos)) {
                header('Location: ' . BASE_URL . '/servicios?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/servicios?msg=error');
            }
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $database = new Database();
            $db = $database->connect();
            $servicioModel = new Servicio($db);
            
            if ($servicioModel->eliminar($_GET['id'])) {
                header('Location: ' . BASE_URL . '/servicios?msg=eliminado');
            } else {
                header('Location: ' . BASE_URL . '/servicios?msg=error');
            }
        }
    }
}
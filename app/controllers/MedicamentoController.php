<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Medicamento.php';

class MedicamentoController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // SEGURIDAD: Permitir Admin (1) y Recepción (4)
        // Agregamos $_SESSION['user_role_id'] == 4 para que la recepcionista pueda ver el stock
        if (!isset($_SESSION['user_role_id']) || ($_SESSION['user_role_id'] != 1 && $_SESSION['user_role_id'] != 4)) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        $medModel = new Medicamento($db);
        
        $resultado = $medModel->leer();
        
        require_once APP_ROOT . '/views/admin/medicamentos.php';
    }

    public function guardar() {
        // SEGURIDAD: Solo Admin puede crear
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/medicamentos'); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->connect();
            $medModel = new Medicamento($db);

            $datos = [
                'nombre_comercial' => $_POST['nombre_comercial'],
                'nombre_generico' => $_POST['nombre_generico'],
                'presentacion' => $_POST['presentacion'],
                'stock' => $_POST['stock']
            ];

            if($medModel->crear($datos)) {
                header('Location: ' . BASE_URL . '/medicamentos?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/medicamentos?msg=error');
            }
        }
    }

    public function actualizar() {
        // SEGURIDAD: Solo Admin puede editar
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/medicamentos'); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->connect();
            $medModel = new Medicamento($db);

            $datos = [
                'id' => $_POST['id_medicamento'],
                'nombre_comercial' => $_POST['nombre_comercial'],
                'nombre_generico' => $_POST['nombre_generico'],
                'presentacion' => $_POST['presentacion'],
                'stock' => $_POST['stock'],
                'estado' => $_POST['estado']
            ];

            if($medModel->actualizar($datos)) {
                header('Location: ' . BASE_URL . '/medicamentos?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/medicamentos?msg=error');
            }
        }
    }

    public function eliminar() {
        // SEGURIDAD: Solo Admin puede borrar
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/medicamentos'); exit; }

        if (isset($_GET['id'])) {
            $database = new Database();
            $db = $database->connect();
            $medModel = new Medicamento($db);
            
            if ($medModel->eliminar($_GET['id'])) {
                header('Location: ' . BASE_URL . '/medicamentos?msg=eliminado');
            } else {
                header('Location: ' . BASE_URL . '/medicamentos?msg=error');
            }
        }
    }
}
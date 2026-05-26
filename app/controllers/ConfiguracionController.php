<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Configuracion.php';

class ConfiguracionController {
    
    public function index() {
        // Seguridad: Solo Admin (Rol 1)
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $configModel = new Configuracion($db);
        $datos = $configModel->obtener();
        
        require_once APP_ROOT . '/views/admin/configuracion.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            // Seguridad extra en el guardado
            if ($_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/home'); exit; }

            $database = new Database();
            $db = $database->connect();
            $configModel = new Configuracion($db);

            $logoNombre = null;

            // Procesar Logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $archivo = $_FILES['logo'];
                $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $permitidos = ['png', 'jpg', 'jpeg'];

                if (in_array($ext, $permitidos)) {
                    $carpeta = APP_ROOT . '/../public/uploads/';
                    if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);
                    
                    $logoNombre = 'logo_clinica_' . time() . '.' . $ext; // Nombre único con tiempo para evitar caché
                    move_uploaded_file($archivo['tmp_name'], $carpeta . $logoNombre);
                }
            }

            $datos = [
                'nombre' => $_POST['nombre'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'moneda' => $_POST['moneda'],
                'logo' => $logoNombre
            ];

            if($configModel->actualizar($datos)) {
                header('Location: ' . BASE_URL . '/configuracion?msg=guardado');
            } else {
                header('Location: ' . BASE_URL . '/configuracion?msg=error');
            }
        }
    }
}
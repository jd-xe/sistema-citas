<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Usuario.php';

class PerfilController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        $usuarioModel = new Usuario($db);
        
        $usuario = $usuarioModel->getById($_SESSION['user_id']);
        
        require_once APP_ROOT . '/views/admin/perfil.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $id = $_SESSION['user_id'];
            
            $nombre = $_POST['nombre'];
            $password_actual = $_POST['password_actual'];
            $password_nueva = $_POST['password_nueva'];
            $password_confirmar = $_POST['password_confirmar'];

            $database = new Database();
            $db = $database->connect();
            $usuarioModel = new Usuario($db);

            // 1. Verificar contraseña actual
            $usuarioActual = $usuarioModel->getById($id);
            if (!password_verify($password_actual, $usuarioActual['password'])) {
                header('Location: ' . BASE_URL . '/perfil?msg=error_pass');
                exit;
            }

            // 2. Verificar cambio de contraseña
            $passParaGuardar = null;
            if (!empty($password_nueva)) {
                if ($password_nueva !== $password_confirmar) {
                    header('Location: ' . BASE_URL . '/perfil?msg=no_match');
                    exit;
                }
                $passParaGuardar = $password_nueva;
            }

            // 3. PROCESAR FOTO DE PERFIL (Avatar)
            $avatarNombre = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $archivo = $_FILES['avatar'];
                $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $permitidos = ['jpg', 'jpeg', 'png'];

                if (in_array($ext, $permitidos)) {
                    $carpeta = APP_ROOT . '/../public/uploads/';
                    if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);
                    
                    // Nombre único: avatar_ID_TIMESTAMP.jpg
                    $avatarNombre = 'avatar_' . $id . '_' . time() . '.' . $ext;
                    move_uploaded_file($archivo['tmp_name'], $carpeta . $avatarNombre);
                    
                    // Actualizamos sesión para que se vea al instante
                    $_SESSION['user_avatar'] = $avatarNombre;
                }
            }

            // 4. Guardar en BD
            if ($usuarioModel->actualizar($id, $nombre, $passParaGuardar, $avatarNombre)) {
                $_SESSION['user_name'] = $nombre;
                header('Location: ' . BASE_URL . '/perfil?msg=guardado');
            } else {
                header('Location: ' . BASE_URL . '/perfil?msg=error');
            }
        }
    }
}
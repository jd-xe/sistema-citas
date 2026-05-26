<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Usuario.php';
require_once APP_ROOT . '/models/Medico.php';

class AuthController {
    
    public function login() {
        require_once APP_ROOT . '/views/auth/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            $database = new Database();
            $db = $database->connect();
            $usuarioModel = new Usuario($db);
            
            $user = $usuarioModel->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                
                // Regenerar ID para seguridad
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol_nombre'];
                $_SESSION['user_role_id'] = $user['id_rol'];
                $_SESSION['user_avatar'] = $user['avatar'];

                // Lógica Médicos
                if ($user['id_rol'] == 2) {
                    $stmt = $db->prepare("SELECT id_medico FROM medicos WHERE id_usuario = :uid");
                    $stmt->execute(['uid' => $user['id_usuario']]);
                    $medicoData = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($medicoData) $_SESSION['medico_id'] = $medicoData['id_medico'];
                }
                
                // CRÍTICO: Guardar y cerrar sesión antes de redirigir
                session_write_close();
                
                header('Location: ' . BASE_URL . '/home');
                exit;
            } else {
                header('Location: ' . BASE_URL . '/login?error=1');
                exit;
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = array();
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Medico.php';
require_once APP_ROOT . '/models/Especialidad.php';
require_once APP_ROOT . '/models/Auditoria.php';

class MedicoController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_role_id']) || !in_array($_SESSION['user_role_id'], [1, 4])) { 
            header('Location: ' . BASE_URL . '/home'); 
            exit; 
        }

        $database = new Database();
        $db = $database->connect();
        
        $medicoModel = new Medico($db);
        $especialidadModel = new Especialidad($db);
        
        $medicos = $medicoModel->leer();
        $especialidades = $especialidadModel->leer();
        
        require_once APP_ROOT . '/views/admin/medicos.php';
    }

    public function guardar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // SOLO ADMIN (1) PUEDE GUARDAR
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/home'); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $medicoModel = new Medico($db);
            $datos = ['nombre' => $_POST['nombre'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'id_especialidad' => $_POST['id_especialidad'], 'colegiatura' => $_POST['colegiatura']];
            if($medicoModel->crear($datos)) header('Location: ' . BASE_URL . '/medicos?msg=creado'); else header('Location: ' . BASE_URL . '/medicos?msg=error');
        }
    }

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // SOLO ADMIN (1) PUEDE ACTUALIZAR
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/home'); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $medicoModel = new Medico($db);
            $datos = ['id_medico' => $_POST['id_medico'], 'id_usuario' => $_POST['id_usuario'], 'nombre' => $_POST['nombre'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'id_especialidad' => $_POST['id_especialidad'], 'colegiatura' => $_POST['colegiatura']];
            if($medicoModel->actualizar($datos)) header('Location: ' . BASE_URL . '/medicos?msg=actualizado'); else header('Location: ' . BASE_URL . '/medicos?msg=error');
        }
    }

    public function cambiarEstado() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // SOLO ADMIN (1) PUEDE CAMBIAR ESTADO
        if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) { header('Location: ' . BASE_URL . '/home'); exit; }

        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $database = new Database(); $db = $database->connect(); $medicoModel = new Medico($db);
            $id = $_GET['id']; $estado = intval($_GET['estado']); $nuevo = ($estado == 1) ? 0 : 1;
            if ($medicoModel->cambiarEstado($id, $nuevo)) { $msg = ($nuevo == 1) ? 'activado' : 'desactivado'; header('Location: ' . BASE_URL . '/medicos?msg=' . $msg); } else header('Location: ' . BASE_URL . '/medicos?msg=error');
        } else header('Location: ' . BASE_URL . '/medicos');
    }

    // --- FUNCIÓN HORARIOS CORREGIDA ---
    public function horarios() {
        if (!isset($_GET['id'])) { header('Location: ' . BASE_URL . '/medicos'); exit; }
        
        $database = new Database(); $db = $database->connect();
        $medicoModel = new Medico($db);
        
        $id_medico = $_GET['id'];
        
        // 1. Obtener los horarios
        $horarios = $medicoModel->obtenerHorarios($id_medico);
        
        // 2. OBTENER DATOS DEL MÉDICO (CORRECCIÓN)
        // Esto define la variable $medico que la vista necesita
        $medico = $medicoModel->obtenerPorId($id_medico);

        if (!$medico) { header('Location: ' . BASE_URL . '/medicos'); exit; }

        require_once APP_ROOT . '/views/admin/horarios_medico.php';
    }

    public function guardarHorario() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $medicoModel = new Medico($db);
            $medicoModel->agregarHorario($_POST['id_medico'], $_POST['dia'], $_POST['inicio'], $_POST['fin']);
            header('Location: ' . BASE_URL . '/medicos/horarios?id=' . $_POST['id_medico']);
        }
    }

    public function eliminarHorario() {
        if (isset($_GET['id']) && isset($_GET['id_medico'])) {
            $database = new Database(); $db = $database->connect(); $medicoModel = new Medico($db);
            $medicoModel->eliminarHorario($_GET['id']);
            header('Location: ' . BASE_URL . '/medicos/horarios?id=' . $_GET['id_medico']);
        }
    }
}
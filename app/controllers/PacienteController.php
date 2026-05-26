<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Paciente.php';
require_once APP_ROOT . '/models/Cita.php'; 

class PacienteController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database();
        $db = $database->connect();
        $pacienteModel = new Paciente($db);
        
        $pacientes = $pacienteModel->leer();
        
        require_once APP_ROOT . '/views/admin/pacientes.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); 
            $db = $database->connect(); 
            $pacienteModel = new Paciente($db);
            
            $datos = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                // Capturamos la contraseña (si viene vacía, el modelo usará el DNI)
                'password' => $_POST['password'] ?? null, 
                'telefono' => $_POST['telefono'],
                'documento_identidad' => $_POST['documento_identidad'],
                'grupo_sanguineo' => $_POST['grupo_sanguineo'],
                'alergias' => $_POST['alergias'],
                'enfermedades_cronicas' => $_POST['enfermedades_cronicas']
            ];

            if ($pacienteModel->crear($datos)) {
                header('Location: ' . BASE_URL . '/pacientes?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/pacientes?msg=error');
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); 
            $db = $database->connect(); 
            $pacienteModel = new Paciente($db);
            
            $datos = [
                'id_usuario' => $_POST['id_usuario'], 
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'documento_identidad' => $_POST['documento_identidad'],
                'grupo_sanguineo' => $_POST['grupo_sanguineo'],
                'alergias' => $_POST['alergias'],
                'enfermedades_cronicas' => $_POST['enfermedades_cronicas'],
                // Solo enviamos password si el usuario escribió algo
                'password' => !empty($_POST['password']) ? $_POST['password'] : null
            ];

            if ($pacienteModel->actualizar($datos)) {
                header('Location: ' . BASE_URL . '/pacientes?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/pacientes?msg=error');
            }
        }
    }

    public function cambiarEstado() {
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $database = new Database(); $db = $database->connect(); $pacienteModel = new Paciente($db);
            $nuevoEstado = ($_GET['estado'] == 1) ? 0 : 1;
            $pacienteModel->cambiarEstado($_GET['id'], $nuevoEstado);
            header('Location: ' . BASE_URL . '/pacientes?msg=actualizado');
        }
    }

    public function historial() {
        if (!isset($_GET['id'])) { header('Location: ' . BASE_URL . '/pacientes'); exit; }
        
        $database = new Database(); 
        $db = $database->connect();
        $pacienteModel = new Paciente($db); 
        $citaModel = new Cita($db);
        
        $paciente = $pacienteModel->obtenerPorId($_GET['id']);
        if(!$paciente) { header('Location: ' . BASE_URL . '/pacientes'); exit; }

        // Historial de citas
        $historial = $citaModel->leer(null, null, null, null, $_GET['id']);
        
        // Archivos adjuntos
        $stmtArchivos = $db->prepare("SELECT * FROM archivos_paciente WHERE id_paciente = :id ORDER BY fecha_subida DESC");
        $stmtArchivos->bindParam(':id', $_GET['id']);
        $stmtArchivos->execute();
        $archivos = $stmtArchivos->fetchAll(PDO::FETCH_ASSOC);

        // Apunta a tu archivo existente
        require_once APP_ROOT . '/views/admin/historial_clinico.php';
    }

    public function subirArchivo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
            $id_paciente = $_POST['id_paciente'];
            $nombre_archivo = $_FILES['archivo']['name'];
            $ruta_temp = $_FILES['archivo']['tmp_name'];
            
            $directorio = dirname(__DIR__, 2) . '/public/uploads/';
            if (!file_exists($directorio)) { mkdir($directorio, 0777, true); }
            
            $nombre_final = time() . '_' . $nombre_archivo;
            $ruta_destino = $directorio . $nombre_final;
            
            if (move_uploaded_file($ruta_temp, $ruta_destino)) {
                $database = new Database(); $db = $database->connect();
                $stmt = $db->prepare("INSERT INTO archivos_paciente (id_paciente, nombre_archivo, ruta_archivo, tipo_archivo) VALUES (:id, :nom, :ruta, :tipo)");
                $tipo = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
                $ruta_relativa = '/uploads/' . $nombre_final;
                
                $stmt->bindParam(':id', $id_paciente); 
                $stmt->bindParam(':nom', $nombre_archivo); 
                $stmt->bindParam(':ruta', $ruta_relativa); 
                $stmt->bindParam(':tipo', $tipo);
                $stmt->execute();
            }
            header('Location: ' . BASE_URL . '/pacientes/historial?id=' . $id_paciente);
        }
    }
}
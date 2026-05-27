<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Medico.php';
require_once APP_ROOT . '/models/Paciente.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Auditoria.php';
require_once APP_ROOT . '/models/Auditoria.php';

class CitaController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database(); $db = $database->connect();
        
        $citaModel = new Cita($db);
        $medicoModel = new Medico($db);
        $pacienteModel = new Paciente($db);
        $servicioModel = new Servicio($db);
        
        $rol = $_SESSION['user_role_id'] ?? 0;
        $userId = $_SESSION['user_id'];
        $medicoId = $_SESSION['medico_id'] ?? null;

        // Filtros de fecha (Rango)
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-d'); // Desde hoy
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d', strtotime('+30 days'));
        
        $estado = $_GET['estado'] ?? null;
        if($estado == "") $estado = null;

        // Consultar según Rol
        if ($rol == 1 || $rol == 4) { // Admin y Recepción
            $resultado = $citaModel->leer($fecha_desde, $fecha_hasta, $estado);
        } elseif ($rol == 2) { // Médico
            $resultado = $citaModel->leer($fecha_desde, $fecha_hasta, $estado, $medicoId);
        } elseif ($rol == 3) { // Paciente
            $resultado = $citaModel->leer(null, null, null, null, $userId);
        } else {
            $resultado = null;
        }

        $medicos = $medicoModel->leer();
        #$pacientes = $pacienteModel->leer();
        if ($rol == 3) {
            $pacientes = null; // Un paciente no necesita ver a los demás
        } else {
            $pacientes = $pacienteModel->leer(); // Admin y Recepción sí la necesitan
        }
        $servicios = $servicioModel->leer();

        require_once APP_ROOT . '/views/admin/citas.php';
    }

    // --- AJAX: Verificar disponibilidad horaria ---
    public function verificarHorarios() {
        if (isset($_GET['id_medico']) && isset($_GET['fecha'])) {
            $database = new Database(); $db = $database->connect(); $citaModel = new Cita($db);
            
            $ocupados = $citaModel->obtenerHorariosOcupados($_GET['id_medico'], $_GET['fecha']);
            
            header('Content-Type: application/json');
            echo json_encode($ocupados);
            exit;
        }
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect();
            $citaModel = new Cita($db);
            $medicoModel = new Medico($db);
            $auditoriaModel = new Auditoria($db); 

            $id_medico = $_POST['id_medico'];
            $fecha_cita = $_POST['fecha'] . ' ' . $_POST['hora'];

            // 1. Validar horario laboral
            if (!$medicoModel->verificaHorarioLaboral($id_medico, $fecha_cita)) {
                header('Location: ' . BASE_URL . '/citas?msg=fuera_horario'); exit;
            }
            // 2. Validar que no esté ocupado
            if ($citaModel->verificarDisponibilidad($id_medico, $fecha_cita)) {
                header('Location: ' . BASE_URL . '/citas?msg=ocupado'); exit;
            }

            $datos = [
                'id_paciente' => $_POST['id_paciente'], 'id_medico' => $id_medico,
                'id_servicio' => $_POST['id_servicio'], 'fecha_cita' => $fecha_cita,
                'motivo' => $_POST['motivo']
            ];

            if($citaModel->crear($datos)) {
                
                // REGISTRAR EN BITÁCORA
                $auditoriaModel->registrar(
                    $_SESSION['user_id'], 
                    'CREAR', 
                    'citas', 
                    null, 
                    'Agendó nueva cita para el paciente ID: ' . $_POST['id_paciente']
                );

                header('Location: ' . BASE_URL . '/citas?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $citaModel = new Cita($db);
            $datos = [
                'id_cita' => $_POST['id_cita'], 'id_medico' => $_POST['id_medico'],
                'id_servicio' => $_POST['id_servicio'],
                'fecha_cita' => $_POST['fecha'] . ' ' . $_POST['hora'],
                'motivo' => $_POST['motivo'], 'estado' => $_POST['estado']
            ];
            if($citaModel->actualizar($datos)) header('Location: ' . BASE_URL . '/citas?msg=actualizado');
            else header('Location: ' . BASE_URL . '/citas?msg=error');
        }
    }

    public function finalizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $citaModel = new Cita($db);
            $fecha_fin = (!empty($_POST['dias_reposo']) && $_POST['dias_reposo'] > 0) ? date('Y-m-d', strtotime("+" . $_POST['dias_reposo'] . " days")) : null;
            if($citaModel->finalizarAtencion($_POST['id_cita'], $_POST['diagnostico'], $_POST['prescripcion'], $_POST['peso'], $_POST['talla'], $_POST['presion'], $_POST['temperatura'], $_POST['dias_reposo'], $fecha_fin)) {
                header('Location: ' . BASE_URL . '/citas?msg=atendido');
            } else header('Location: ' . BASE_URL . '/citas?msg=error');
        }
    }

    public function eliminar() {
        if (session_status() === PHP_SESSION_NONE) session_start(); // Necesario para capturar al usuario logueado
        
        if (isset($_GET['id'])) {
            $database = new Database(); 
            $db = $database->connect(); 
            $citaModel = new Cita($db);
            $auditoriaModel = new Auditoria($db); // Instanciamos auditoría
            
            if ($citaModel->eliminar($_GET['id'])) {
                
                // --- INICIO AUDITORÍA ---
                $auditoriaModel->registrar(
                    $_SESSION['user_id'], 
                    'ELIMINAR', 
                    'citas', 
                    $_GET['id'], 
                    'Canceló la cita médica con ID: ' . $_GET['id']
                );
                // --- FIN AUDITORÍA ---

                header('Location: ' . BASE_URL . '/citas?msg=eliminado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }

    public function listarEventos() {}
    public function cobrar() { if(isset($_GET['id'])) header('Location: ' . BASE_URL . '/caja?cita_id=' . $_GET['id']); }
}
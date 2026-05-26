<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Caja.php';
require_once APP_ROOT . '/models/Cita.php'; // Necesario para leer datos de la cita

class CajaController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'];

        $database = new Database();
        $db = $database->connect();
        $cajaModel = new Caja($db);
        $citaModel = new Cita($db);
        
        $sesionActiva = $cajaModel->obtenerSesionActiva($userId);

        if (!$sesionActiva) {
            require_once APP_ROOT . '/views/admin/caja_apertura.php';
        } else {
            // Datos del Dashboard
            $balance = $cajaModel->obtenerBalance($sesionActiva['id_sesion'], $sesionActiva['fecha_apertura']);
            $gastos = $cajaModel->listarGastos($sesionActiva['id_sesion']);
            $ingresosRecientes = $cajaModel->listarIngresosRecientes();
            
            $saldoInicial = $sesionActiva['monto_apertura'];
            $totalIngresos = $balance['ingresos'];
            $totalGastos = $balance['gastos'];
            $saldoActual = ($saldoInicial + $totalIngresos) - $totalGastos;

            // --- LÓGICA DE COBRO AUTOMÁTICO ---
            $datosCobro = null;
            if (isset($_GET['cita_id'])) {
                // Buscamos los datos para llenar el modal automáticamente
                $datosCobro = $citaModel->obtenerParaCobro($_GET['cita_id']);
            }

            require_once APP_ROOT . '/views/admin/caja_dashboard.php';
        }
    }

    public function guardarCobro() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->connect();
            $cajaModel = new Caja($db);

            $id_cita = $_POST['id_cita'];
            $monto = $_POST['monto'];
            $metodo = $_POST['metodo_pago'];
            $obs = $_POST['observaciones'];

            if ($cajaModel->registrarCobro($id_cita, $monto, $metodo, $obs)) {
                // Redirigir a caja limpio (sin ID) y con mensaje de éxito
                header('Location: ' . BASE_URL . '/caja?msg=cobro_ok');
            } else {
                header('Location: ' . BASE_URL . '/caja?msg=error');
            }
        }
    }

    public function abrir() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $cajaModel = new Caja($db);
            session_start();
            if ($cajaModel->abrirCaja($_SESSION['user_id'], $_POST['monto_apertura'])) {
                header('Location: ' . BASE_URL . '/caja?msg=apertura_ok');
            } else {
                header('Location: ' . BASE_URL . '/caja?msg=error');
            }
        }
    }

    public function registrarGasto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $cajaModel = new Caja($db);
            if ($cajaModel->registrarGasto($_POST['id_sesion'], $_POST['descripcion'], $_POST['monto'])) {
                header('Location: ' . BASE_URL . '/caja?msg=gasto_ok');
            } else {
                header('Location: ' . BASE_URL . '/caja?msg=error');
            }
        }
    }

    public function cerrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database(); $db = $database->connect(); $cajaModel = new Caja($db);
            if ($cajaModel->cerrarCaja($_POST['id_sesion'], $_POST['monto_cierre'], $_POST['observaciones'])) {
                header('Location: ' . BASE_URL . '/caja?msg=cierre_ok');
            } else {
                header('Location: ' . BASE_URL . '/caja?msg=error');
            }
        }
    }
}
<?php
// 1. INICIO DE CONFIGURACIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mostrar errores (Cambiar a 0 en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. CONSTANTES Y URL BASE
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptPath = rtrim($scriptPath, '/'); 

define('BASE_URL', $protocol . $domainName . $scriptPath);
define('APP_ROOT', dirname(__DIR__) . '/app');

// 3. AUTOCARGA DE CONTROLADORES
$controladores = [
    'AuthController', 
    'HomeController', 
    'CitaController', 
    'MedicoController', 
    'PacienteController', 
    'ServicioController', 
    'PagoController', 
    'CajaController',
    'ConfiguracionController', 
    'PerfilController',
    'ReporteController',
    'AuditoriaController',
    'EspecialidadController',
    'MedicamentoController',
    'SistemaController'
];

foreach ($controladores as $controlador) {
    if (file_exists(APP_ROOT . '/controllers/' . $controlador . '.php')) {
        require_once APP_ROOT . '/controllers/' . $controlador . '.php';
    }
}

// 4. ROUTER
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = dirname($_SERVER['SCRIPT_NAME']);
$url = (strpos($request_uri, $base_path) === 0) ? substr($request_uri, strlen($base_path)) : $request_uri;
$url = trim($url, '/');

if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}

$urlArray = explode('/', $url);
$controllerName = !empty($urlArray[0]) ? $urlArray[0] : 'home';

// 5. SEGURIDAD
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId && $controllerName !== 'login' && $controllerName !== 'auth') {
    $controllerName = 'login'; 
}

if ($userId && $controllerName === 'login') {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

// 6. SWITCH DE RUTAS
switch ($controllerName) {
    
    case 'auth':
        if (class_exists('AuthController')) {
            $c = new AuthController();
            if (isset($urlArray[1]) && $urlArray[1] == 'authenticate') $c->authenticate();
            elseif (isset($urlArray[1]) && $urlArray[1] == 'logout') $c->logout();
        }
        break;

    case 'login':
        if (class_exists('AuthController')) {
            $c = new AuthController();
            $c->login();
        }
        break;

    case 'home':
        if (class_exists('HomeController')) {
            $c = new HomeController();
            $c->index();
        }
        break;

    case 'sistema':
        if (class_exists('SistemaController')) {
            $c = new SistemaController();
            if (isset($urlArray[1]) && $urlArray[1] == 'ejecutarReset') $c->ejecutarReset();
            else $c->index();
        }
        break;

    case 'pacientes':
        if (class_exists('PacienteController')) {
            $c = new PacienteController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'eliminar') $c->eliminar();
                elseif ($urlArray[1] == 'cambiarEstado') $c->cambiarEstado();
                elseif ($urlArray[1] == 'historial') $c->historial();
                elseif ($urlArray[1] == 'subirArchivo') $c->subirArchivo();
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    case 'medicos':
        if (class_exists('MedicoController')) {
            $c = new MedicoController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'horarios') $c->horarios();
                elseif ($urlArray[1] == 'guardarHorario') $c->guardarHorario();
                elseif ($urlArray[1] == 'eliminarHorario') $c->eliminarHorario();
                elseif ($urlArray[1] == 'cambiarEstado') $c->cambiarEstado();
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    // --- CITAS (CON LA NUEVA RUTA AJAX) ---
    case 'citas':
        if (class_exists('CitaController')) {
            $c = new CitaController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'finalizar') $c->finalizar();
                elseif ($urlArray[1] == 'eliminar') $c->eliminar();
                elseif ($urlArray[1] == 'listarEventos') $c->listarEventos();
                elseif ($urlArray[1] == 'cobrar') $c->cobrar();
                elseif ($urlArray[1] == 'verificarHorarios') $c->verificarHorarios(); // <--- AQUÍ
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    case 'pagos': 
    case 'caja':  
        if (class_exists('CajaController')) {
            $c = new CajaController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'abrir') $c->abrir();
                elseif ($urlArray[1] == 'registrarGasto') $c->registrarGasto();
                elseif ($urlArray[1] == 'cerrar') $c->cerrar();
                elseif ($urlArray[1] == 'guardarCobro') $c->guardarCobro();
                else $c->index();
            } else {
                $c->index();
            }
        } elseif (class_exists('PagoController')) {
            $c = new PagoController();
            if (isset($urlArray[1]) && $urlArray[1] == 'eliminar') $c->eliminar();
            else $c->index();
        }
        break;

    case 'servicios':
        if (class_exists('ServicioController')) {
            $c = new ServicioController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'eliminar') $c->eliminar();
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    case 'especialidades':
        if (class_exists('EspecialidadController')) {
            $c = new EspecialidadController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'cambiarEstado') $c->cambiarEstado();
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    case 'medicamentos':
        if (class_exists('MedicamentoController')) {
            $c = new MedicamentoController();
            if (isset($urlArray[1])) {
                if ($urlArray[1] == 'guardar') $c->guardar();
                elseif ($urlArray[1] == 'actualizar') $c->actualizar();
                elseif ($urlArray[1] == 'eliminar') $c->eliminar();
                else $c->index();
            } else {
                $c->index();
            }
        }
        break;

    case 'configuracion':
        if (class_exists('ConfiguracionController')) {
            $c = new ConfiguracionController();
            if (isset($urlArray[1]) && $urlArray[1] == 'guardar') $c->guardar();
            else $c->index();
        }
        break;

    case 'perfil':
        if (class_exists('PerfilController')) {
            $c = new PerfilController();
            if (isset($urlArray[1]) && $urlArray[1] == 'actualizar') $c->actualizar();
            else $c->index();
        }
        break;

    case 'reportes':
        if (class_exists('ReporteController')) {
            $c = new ReporteController();
            $c->index();
        }
        break;

    case 'auditoria':
        if (class_exists('AuditoriaController')) {
            $c = new AuditoriaController();
            $c->index();
        }
        break;

    default:
        header('Location: ' . BASE_URL . '/login');
        break;
}
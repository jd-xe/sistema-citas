<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='" . BASE_URL . "/login';</script>";
    exit;
}
$rol = $_SESSION['user_role_id'] ?? 0;
$nombreUsuario = $_SESSION['user_name'] ?? 'Usuario';

// Obtener Configuración Global (Logo y Nombre de Clínica)
if (!class_exists('Database'))
    require_once APP_ROOT . '/config/Database.php';
if (!class_exists('Configuracion'))
    require_once APP_ROOT . '/models/Configuracion.php';

$db_header = new Database();
$conn_header = $db_header->connect();
$configModel_header = new Configuracion($conn_header);
$empresa_header = $configModel_header->obtener();

$nombre_app = !empty($empresa_header['nombre_clinica']) ? $empresa_header['nombre_clinica'] : 'Centro Médico Salud';
$logo_app = !empty($empresa_header['logo']) ? $empresa_header['logo'] : null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nombre_app; ?> - Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        /* SIDEBAR: Visible por defecto en escritorio */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            margin-left: 0;
            /* VISIBLE */
            transition: margin 0.25s ease-out;
            background-color: #1e2125;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.1rem;
            color: #fff;
            text-align: center;
        }

        #sidebar-wrapper .list-group {
            width: 260px;
        }

        #page-content-wrapper {
            width: 100%;
        }

        /* CLASE PARA OCULTAR EL SIDEBAR */
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: -260px;
            /* OCULTO */
        }

        /* RESPONSIVE: En móviles empieza oculto */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }

        .list-group-item {
            border: none;
            padding: 12px 25px;
            font-size: 0.9rem;
            color: #adb5bd;
            background-color: transparent;
            transition: all 0.3s;
        }

        .list-group-item:hover {
            background-color: #2c3036;
            color: #fff;
            border-left: 4px solid #0d6efd;
        }

        .list-group-item.active {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }

        .list-group-item i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
            background: #fff;
        }

        .item-danger:hover {
            background-color: #dc3545 !important;
            color: white !important;
            border-left: 4px solid #b02a37 !important;
        }

        .item-danger {
            color: #ff6b6b !important;
        }

        /* Estilos del Logo del Sidebar */
        .sidebar-logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            min-height: 120px;
        }

        .sidebar-logo-img {
            max-width: 90%;
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }

        .sidebar-logo-img:hover {
            transform: scale(1.05);
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            text-align: center;
            line-height: 1.2;
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-dark sidebar-logo-container">
                <?php if ($logo_app && file_exists(APP_ROOT . '/../public/uploads/' . $logo_app)): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($logo_app); ?>" alt="Logo"
                        class="sidebar-logo-img">
                <?php else: ?>
                    <div class="mb-2"><i class="fas fa-hospital-user fa-2x text-light"></i></div>
                <?php endif; ?>
                <div class="sidebar-title mt-2"><?php echo htmlspecialchars($nombre_app); ?></div>
            </div>
            <div class="list-group list-group-flush pt-3">

                <a href="<?php echo BASE_URL; ?>/home"
                    class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/home') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i> Inicio
                </a>

                <a href="<?php echo BASE_URL; ?>/citas"
                    class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/citas') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i> Citas
                </a>

                <?php if($rol==1):?>
                    <a href="<?php echo BASE_URL; ?>/especialidades"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/especialidades') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-stethoscope"></i> Especialidades
                    </a>
                    <a href="<?php echo BASE_URL; ?>/servicios"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/servicios') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> Servicios / Tarifas
                    </a>
                <?php endif; ?>

                <?php if ($rol == 1 || $rol == 4): ?>
                    <a href="<?php echo BASE_URL; ?>/medicos"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/medicos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-user-md"></i> Médicos
                    </a>
                    <a href="<?php echo BASE_URL; ?>/medicamentos"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/medicamentos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-pills"></i> Farmacia
                    </a>
                <?php endif; ?>

                <?php 
                    // 1. Validamos si el usuario logueado tiene el rol de Paciente (Rol 3)
                    $esPaciente = (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 3);

                    // 2. Definimos dinámicamente la URL, el texto y el icono según el rol
                    $urlPacientes  = $esPaciente ? BASE_URL . '/pacientes/historial?id=' . $_SESSION['user_id'] : BASE_URL . '/pacientes';
                    $textoPacientes = $esPaciente ? 'Mi Historial Clínico' : 'Pacientes';
                    $iconoPacientes = $esPaciente ? 'fas fa-file-medical' : 'fas fa-users';
                    ?>

                    <a href="<?php echo $urlPacientes; ?>"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/pacientes') !== false ? 'active' : ''; ?>">
                        <i class="<?php echo $iconoPacientes; ?>"></i> <?php echo $textoPacientes; ?>
                    </a>

                <?php if ($rol == 1 || $rol == 4): ?>
                    <a href="<?php echo BASE_URL; ?>/caja"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/caja') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-cash-register"></i> Caja / Pagos
                    </a>
                <?php endif; ?>

                <?php if ($rol == 1): ?>
                    <div class="sidebar-heading text-uppercase fs-6 text-muted mt-3 mb-1"
                        style="font-size: 0.75rem; padding-left: 25px; text-align: left;">Administración</div>
                    <a href="<?php echo BASE_URL; ?>/reportes"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i> Reportes
                    </a>
                    <a href="<?php echo BASE_URL; ?>/auditoria"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/auditoria') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-shield-alt"></i> Seguridad
                    </a>
                    <a href="<?php echo BASE_URL; ?>/configuracion"
                        class="list-group-item list-group-item-action <?php echo strpos($_SERVER['REQUEST_URI'], '/configuracion') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                    <a href="<?php echo BASE_URL; ?>/sistema"
                        class="list-group-item list-group-item-action item-danger <?php echo strpos($_SERVER['REQUEST_URI'], '/sistema') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-database"></i> Sistema (Reset)
                    </a>
                <?php endif; ?>

            </div>
            <div class="mt-auto p-3">
                <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <button class="btn btn-light" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 text-secondary fw-bold d-none d-sm-block">
                        <?php echo htmlspecialchars($nombre_app); ?> - Panel</h5>
                    <div class="ms-auto dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                            id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end me-2 d-none d-lg-block">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Bienvenido,</small>
                                <span class="fw-bold"><?php echo $nombreUsuario; ?></span>
                            </div>
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/perfil"><i
                                        class="fas fa-user-circle me-2 text-primary"></i> Mi Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i
                                        class="fas fa-power-off me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
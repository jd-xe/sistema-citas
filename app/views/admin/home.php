<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<style>
    /* Estilos Premium Mejorados para el Dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .dashboard-header::after {
        content: '\f0f1';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -30px;
        bottom: -50px;
        font-size: 180px;
        color: rgba(13, 110, 253, 0.03);
        transform: rotate(-15deg);
        pointer-events: none;
    }

    .greeting-text {
        font-weight: 800;
        color: #1a202c;
        letter-spacing: -0.5px;
        font-size: 2.2rem;
    }

    .stat-card {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        z-index: 1;
        min-height: 180px;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 100%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.1) 100%);
        transform: skewX(-20deg) translateX(50px);
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: -1;
    }

    .stat-card:hover::after {
        transform: skewX(-20deg) translateX(-180px);
    }

    .stat-card-icon {
        position: absolute;
        right: 25px;
        bottom: 15px;
        font-size: 6rem;
        opacity: 0.1;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .stat-card:hover .stat-card-icon {
        transform: scale(1.15) rotate(8deg);
        opacity: 0.2;
    }

    /* Gradientes Personalizados Más Vibrantes */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
    }

    .table-custom {
        border-radius: 20px;
        overflow: hidden;
    }

    .table-custom thead {
        background-color: #f8f9fa;
        color: #6c757d;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .table-custom tbody tr {
        transition: all 0.2s ease;
    }

    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
        /* Quitamos el scale que puede ser molesto en tablas grandes, usamos background */
    }

    .card-panel {
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.03);
        background: #ffffff;
    }

    .card-panel-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem 1.75rem;
    }

    .badge-custom {
        padding: 0.6em 1em;
        font-weight: 600;
        border-radius: 8px;
        /* Un poco menos redondeado para aspecto profesional */
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    /* Ajustes específicos para las píldoras de fecha/hora */
    .time-badge {
        background-color: #f8f9fa;
        color: #495057;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
        border: 1px solid #e9ecef;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        /* Forma de app moderna (squircle) */
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<div class="dashboard-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h2 class="greeting-text mb-2">¡Hola, <span class="text-primary"><?php echo $_SESSION['user_name']; ?></span>!
            👋</h2>
        <p class="text-muted mb-0 fs-5" style="max-width: 600px;">Aquí está la información general de la clínica al día
            de hoy. Tienes <strong class="text-dark"><?php echo count($proximasCitas); ?></strong> citas próximas en la
            agenda.</p>
    </div>
    <div>
        <div class="d-inline-flex align-items-center bg-white border rounded-pill px-4 py-2 shadow-sm">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width: 32px; height: 32px;">
                <i class="far fa-calendar-alt text-primary"></i>
            </div>
            <span class="fs-6 fw-semibold text-dark"><?php echo date('d \d\e M, Y'); ?></span>
        </div>
    </div>
</div>

<div class="row mb-5 g-4 mt-2">
    <!-- Tarjeta Citas -->
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-primary text-white shadow-lg h-100">
            <div class="card-body p-4 p-xl-5 position-relative z-1 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="bg-white text-primary rounded-3 p-3 shadow-sm" style="opacity: 0.9;">
                        <i class="fas fa-calendar-check fs-3"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white fs-6 px-3 py-2 rounded-pill">+2% Hoy</span>
                </div>
                <div>
                    <h2 class="display-3 fw-bolder mb-1 lh-1"><?php echo $totalCitas; ?></h2>
                    <span class="fs-5 fw-light text-white-50 text-uppercase tracking-wide">Citas Registradas</span>
                </div>
                <i class="fas fa-calendar-week stat-card-icon"></i>
            </div>
        </div>
    </div>

    <!-- Tarjeta Médicos -->
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-success text-white shadow-lg h-100">
            <div class="card-body p-4 p-xl-5 position-relative z-1 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="bg-white text-success rounded-3 p-3 shadow-sm" style="opacity: 0.9;">
                        <i class="fas fa-user-md fs-3"></i>
                    </div>
                </div>
                <div>
                    <h2 class="display-3 fw-bolder mb-1 lh-1"><?php echo $totalMedicos; ?></h2>
                    <span class="fs-5 fw-light text-white-50 text-uppercase tracking-wide">Médicos Activos</span>
                </div>
                <i class="fas fa-stethoscope stat-card-icon"></i>
            </div>
        </div>
    </div>

    <!-- Tarjeta Pacientes -->
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-info text-white shadow-lg h-100">
            <div class="card-body p-4 p-xl-5 position-relative z-1 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="bg-white text-info rounded-3 p-3 shadow-sm" style="opacity: 0.9;">
                        <i class="fas fa-users fs-3"></i>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/pacientes"
                        class="text-white text-decoration-none bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center transition-all hover-scale"
                        style="width: 35px; height: 35px;">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div>
                    <h2 class="display-3 fw-bolder mb-1 lh-1"><?php echo $totalPacientes; ?></h2>
                    <span class="fs-5 fw-light text-white-50 text-uppercase tracking-wide">Pacientes</span>
                </div>
                <i class="fas fa-id-card stat-card-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tabla Próximas Citas -->
    <div class="col-lg-8">
        <div class="card card-panel h-100">
            <div class="card-header card-panel-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-inline-flex align-items-center justify-content-center me-3"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-clock"></i>
                    </div>
                    Próximas Citas (Revisado)
                </h5>
                <a href="<?php echo BASE_URL; ?>/citas" class="btn btn-primary rounded-3 px-4 shadow-sm fw-semibold">
                    Ver Agenda Completa
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Horario</th>
                                <th class="py-3 border-0">Detalles del Paciente</th>
                                <th class="py-3 border-0">Asignación</th>
                                <th class="py-3 text-center border-0">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($proximasCitas)): ?>
                                <?php foreach ($proximasCitas as $index => $cita): ?>
                                    <?php
                                    $esHoy = (date('Y-m-d') == date('Y-m-d', strtotime($cita['fecha_cita'])));
                                    $textoFecha = $esHoy ? '<span class="text-primary fw-bold">Hoy</span>' : date('d/m/Y', strtotime($cita['fecha_cita']));
                                    $hora = date('h:i A', strtotime($cita['fecha_cita']));

                                    $badgeClass = match ($cita['estado']) {
                                        'Pendiente' => 'bg-warning bg-opacity-25 text-warning-emphasis border border-warning-subtle',
                                        'Confirmada' => 'bg-info bg-opacity-25 text-info-emphasis border border-info-subtle',
                                        'Finalizada' => 'bg-success bg-opacity-25 text-success-emphasis border border-success-subtle',
                                        'Cancelada' => 'bg-danger bg-opacity-25 text-danger-emphasis border border-danger-subtle',
                                        default => 'bg-secondary bg-opacity-25 text-secondary-emphasis border border-secondary-subtle'
                                    };

                                    // Obtener inicial para el avatar
                                    $inicial = substr($cita['paciente'], 0, 1);
                                    // Color aleatorio para el avatar basado en el nombre
                                    $colores = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark'];
                                    $colorAvatar = $colores[strlen($cita['paciente']) % count($colores)];

                                    // Bordes inferiores
                                    $isLast = ($index === count($proximasCitas) - 1);
                                    $borderClass = $isLast ? '' : 'border-bottom';
                                    ?>
                                    <tr class="<?php echo $borderClass; ?>">
                                        <td class="ps-4 py-4">
                                            <div class="d-flex flex-column gap-1">
                                                <span class="time-badge shadow-sm"><i
                                                        class="far fa-clock text-primary me-1"></i> <?php echo $hora; ?></span>
                                                <span class="small text-muted ms-1"><i
                                                        class="far fa-calendar text-muted opacity-50 me-1"></i>
                                                    <?php echo $textoFecha; ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar <?php echo $colorAvatar; ?> text-white me-3 shadow-sm">
                                                    <?php echo strtoupper($inicial); ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">
                                                        <?php echo htmlspecialchars($cita['paciente']); ?></h6>
                                                    <span class="small text-muted">ID:
                                                        #<?php echo str_pad($cita['id_cita'], 4, '0', STR_PAD_LEFT); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-2">
                                                    <i class="fas fa-stethoscope text-secondary"></i>
                                                </div>
                                                <span
                                                    class="text-dark fw-medium"><?php echo htmlspecialchars($cita['medico']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="badge badge-custom <?php echo $badgeClass; ?>"><i
                                                    class="fas fa-circle ms-n1 me-1"
                                                    style="font-size: 0.5rem; vertical-align: middle;"></i>
                                                <?php echo $cita['estado']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-5 d-flex flex-column align-items-center justify-content-center">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486776.png" alt="Empty"
                                                style="width: 120px; opacity: 0.5; margin-bottom: 1.5rem;">
                                            <h4 class="fw-bold text-dark mb-2">Todo listo por hoy</h4>
                                            <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">No tienes
                                                nuevas citas programadas. Relájate o revisa los reportes pendientes.</p>
                                            <a href="<?php echo BASE_URL; ?>/citas"
                                                class="btn btn-outline-primary rounded-pill px-4">Ir a Citas</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="col-lg-4">
        <div class="card card-panel h-100">
            <div class="card-header card-panel-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    Resumen Estadístico
                </h5>
                <button class="btn btn-sm btn-light border" onclick="window.location.reload();"><i
                        class="fas fa-sync-alt text-secondary"></i></button>
            </div>
            <div class="card-body p-4 d-flex flex-column">
                <div class="text-center mb-4">
                    <span class="text-muted text-uppercase small fw-bold tracking-wide">Distribución de Estados</span>
                </div>
                <div class="position-relative flex-grow-1 d-flex align-items-center justify-content-center min-h-300"
                    style="min-height: 250px;">
                    <canvas id="chartCitas"></canvas>
                    <!-- Centro del gráfico -->
                    <div class="position-absolute top-50 start-50 translate-middle text-center"
                        style="pointer-events: none; margin-top: -10px;">
                        <span class="text-muted small fw-medium d-block mb-n1">Total</span>
                        <h2 class="fw-bolder text-dark mb-0 display-5">
                            <?php echo array_sum(array_column($statsEstado, 'cantidad')); ?></h2>
                    </div>
                </div>

                <?php if (!empty($statsEstado)): ?>
                    <div class="mt-4 pt-3 border-top">
                        <?php foreach ($statsEstado as $stat):
                            // Mapeo básico para los "dots"
                            $dotColor = match ($stat['estado']) {
                                'Finalizada' => 'text-success',
                                'Confirmada' => 'text-info',
                                'Pendiente' => 'text-warning',
                                'Cancelada' => 'text-danger',
                                default => 'text-primary'
                            };
                            $porcentaje = round(($stat['cantidad'] / array_sum(array_column($statsEstado, 'cantidad'))) * 100);
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-circle <?php echo $dotColor; ?> me-2 small" style="font-size: 0.6rem;"></i>
                                    <span class="text-secondary fw-medium"><?php echo $stat['estado']; ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-dark me-3"><?php echo $stat['cantidad']; ?></span>
                                    <span class="badge bg-light text-secondary rounded-pill"
                                        style="width: 45px;"><?php echo $porcentaje; ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('chartCitas');

        const statsArray = <?php echo json_encode($statsEstado); ?>;
        const labels = statsArray.map(item => item.estado);
        const data = statsArray.map(item => item.cantidad);

        // Configuración de visualización si no hay datos
        if (data.length === 0 || data.reduce((a, b) => parseInt(a) + parseInt(b), 0) === 0) {
            labels.push('Sin Datos');
            data.push(1);
            var bgColors = ['#f8f9fa'];
            var hoverColors = ['#e9ecef'];
        } else {
            // Colores correspondientes a Bootstrap pero más limpios
            var bgColors = labels.map(estado => {
                if (estado === 'Finalizada') return '#198754';
                if (estado === 'Confirmada') return '#0dcaf0';
                if (estado === 'Pendiente') return '#ffc107';
                if (estado === 'Cancelada') return '#dc3545';
                return '#0d6efd';
            });
            // Tonos muy difuminados para el hover, logrando un efecto de "sombra interior" al arco
            var hoverColors = labels.map(estado => {
                if (estado === 'Finalizada') return '#157347';
                if (estado === 'Confirmada') return '#0bacce';
                if (estado === 'Pendiente') return '#d39e00';
                if (estado === 'Cancelada') return '#bb2d3b';
                return '#0a58ca';
            });
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    hoverBackgroundColor: hoverColors,
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 8,
                    borderRadius: 5 // Redondear los extremos de los arcos
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%', // Agujero más grande para diseño moderno
                plugins: {
                    legend: {
                        display: false // Ocultamos leyenda nativa, añadimos HTML perosnalizado abajo
                    },
                    tooltip: {
                        backgroundColor: '#1a202c',
                        titleColor: '#f8f9fa',
                        bodyColor: '#e2e8f0',
                        padding: 15,
                        cornerRadius: 10,
                        displayColors: true,
                        usePointStyle: true,
                        callbacks: {
                            label: function (context) {
                                let val = context.parsed;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((val / total) * 100);
                                return ` ${val} Registros (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeOutBounce'
                }
            }
        });
    });
</script>
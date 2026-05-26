<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php 
    $rol = $_SESSION['user_role_id'] ?? 0; 
    $nombre = $_SESSION['user_name'] ?? 'Usuario';
    
    // Saludo según la hora
    $hora = date('H');
    $saludo = ($hora < 12) ? 'Buenos días' : (($hora < 18) ? 'Buenas tardes' : 'Buenas noches');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><?php echo $saludo . ', ' . $nombre; ?></h3>
        <p class="text-muted small">Bienvenido a tu panel de control.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <h5 class="fw-bold mb-0 text-primary"><?php echo date('h:i A'); ?></h5>
        <small class="text-muted"><?php echo date('d/m/Y'); ?></small>
    </div>
</div>

<?php if($rol == 1): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0"><?php echo $data['total_citas']; ?></h2>
                    <small class="opacity-75">Citas Totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0 text-success"><?php echo $data['total_medicos']; ?></h2>
                    <small class="text-muted">Médicos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0 text-info"><?php echo $data['total_pacientes']; ?></h2>
                    <small class="text-muted">Pacientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-dark text-white h-100">
                <div class="card-body text-center py-3">
                    <a href="<?php echo BASE_URL; ?>/citas" class="btn btn-light btn-sm fw-bold w-100 mb-2">Agenda</a>
                    <a href="<?php echo BASE_URL; ?>/reportes" class="btn btn-outline-light btn-sm w-100">Reportes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold d-flex justify-content-between">
                    <span>Actividad de Hoy</span>
                    <span class="badge bg-light text-dark"><?php echo date('d/m/Y'); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th>Hora</th><th>Paciente</th><th>Médico</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                                <?php if($data['citas_hoy']->rowCount() > 0): ?>
                                    <?php while($cita = $data['citas_hoy']->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?php echo date('H:i', strtotime($cita['fecha_cita'])); ?></td>
                                        <td><?php echo $cita['paciente']; ?></td>
                                        <td class="small"><?php echo $cita['medico']; ?></td>
                                        <td>
                                            <?php 
                                            $st = $cita['estado'];
                                            $badge = ($st=='Pendiente')?'bg-warning':(($st=='Confirmada')?'bg-primary':(($st=='Finalizada')?'bg-success':'bg-danger'));
                                            ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo $st; ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No hay citas programadas para hoy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Resumen Global</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="adminChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('adminChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($data['chart_labels']); ?>,
                datasets: [{
                    data: <?php echo json_encode($data['chart_data']); ?>,
                    backgroundColor: <?php echo json_encode($data['chart_colors']); ?>,
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    </script>


<?php elseif($rol == 2): ?>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 bg-primary text-white mb-4" style="background: linear-gradient(45deg, #005bea, #00c6fb);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4>Mi Agenda del Día</h4>
                            <p class="mb-0 opacity-75">Tienes <strong><?php echo $data['pendientes_hoy']; ?></strong> pacientes pendientes para hoy.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-user-md fa-4x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Pacientes Citados (<?php echo date('d/m/Y'); ?>)</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Motivo</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($data['citas_hoy']->rowCount() > 0): ?>
                                    <?php while($cita = $data['citas_hoy']->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-bold text-primary fs-5"><?php echo date('H:i', strtotime($cita['fecha_cita'])); ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $cita['paciente']; ?></div>
                                            <small class="text-muted"><?php echo $cita['paciente_telefono']; ?></small>
                                        </td>
                                        <td class="small text-muted"><?php echo substr($cita['motivo'], 0, 30); ?>...</td>
                                        <td class="text-center">
                                            <?php if($cita['estado'] == 'Finalizada'): ?>
                                                <span class="badge bg-success"><i class="fas fa-check"></i> Atendido</span>
                                            <?php else: ?>
                                                <a href="<?php echo BASE_URL; ?>/citas" class="btn btn-sm btn-success fw-bold rounded-pill px-3">
                                                    <i class="fas fa-stethoscope me-1"></i> Atender
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No tienes pacientes agendados para hoy. ¡Disfruta tu día!</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small fw-bold">Atendidos Hoy</h6>
                    <h1 class="fw-bold text-success mb-0"><?php echo $data['atendidos_hoy']; ?></h1>
                </div>
            </div>
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small fw-bold">Total Histórico</h6>
                    <h3 class="fw-bold text-dark mb-0"><?php echo $data['total_mis_citas']; ?></h3>
                    <small class="text-muted">Consultas realizadas</small>
                </div>
            </div>
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body p-4 text-center">
                    <p class="mb-3">¿Necesitas bloquear un horario?</p>
                    <a href="<?php echo BASE_URL; ?>/medicos/horarios?id=<?php echo $_SESSION['medico_id']; ?>" class="btn btn-outline-light btn-sm w-100">Gestionar Mis Turnos</a>
                </div>
            </div>
        </div>
    </div>


<?php elseif($rol == 3): ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if($data['proxima_cita']): $cita = $data['proxima_cita']; ?>
                <div class="card shadow border-0 mb-4 border-start border-5 border-primary">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-uppercase text-primary fw-bold ls-1">Tu Próxima Consulta</h6>
                                <h2 class="fw-bold mb-0"><?php echo date('d', strtotime($cita['fecha_cita'])); ?> de <?php echo date('M', strtotime($cita['fecha_cita'])); ?></h2>
                                <span class="text-muted fs-5">a las <?php echo date('h:i A', strtotime($cita['fecha_cita'])); ?></span>
                            </div>
                            <div class="text-center bg-light rounded p-3">
                                <i class="fas fa-user-md fa-2x text-secondary mb-2"></i><br>
                                <small class="fw-bold text-dark">Dr. <?php echo $cita['medico']; ?></small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Consultorio 1</span>
                            <span class="badge bg-warning text-dark">
                                <?php echo $cita['estado']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0 mb-4 bg-light text-center p-5">
                    <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                    <h4>¡Estás al día!</h4>
                    <p class="text-muted">No tienes citas programadas próximamente.</p>
                    <button class="btn btn-primary fw-bold px-4 mt-2" data-bs-toggle="modal" data-bs-target="#modalCita">
                        <i class="fas fa-plus me-2"></i> Agendar Nueva Cita
                    </button>
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-file-medical-alt fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Mi Historial</h6>
                                <a href="<?php echo BASE_URL; ?>/pacientes/historial?id=<?php echo $_SESSION['user_id']; ?>" class="stretched-link text-decoration-none text-muted small">Ver diagnósticos pasados</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-plus-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Nueva Cita</h6>
                                <a href="<?php echo BASE_URL; ?>/citas" class="stretched-link text-decoration-none text-muted small">Reservar con un especialista</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

<?php endif; ?>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
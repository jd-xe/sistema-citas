<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-white bg-primary h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="mb-0 text-white-50">Citas Totales (Filtro Actual)</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $resultado ? $resultado->rowCount() : 0; ?></h2>
                </div>
                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-white bg-success h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="mb-0 text-white-50">Staff Médico Disponible</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $medicos ? $medicos->rowCount() : 0; ?></h2>
                </div>
                <i class="fas fa-user-md fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/citas" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="<?php echo $fecha_desde; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="<?php echo $fecha_hasta; ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="Pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Finalizada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                    <option value="Cancelada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar"><i class="fas fa-filter"></i></button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                    <i class="fas fa-plus"></i> Nueva Cita
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Fecha / Hora</th>
                        <th>Paciente</th>
                        <th>Médico / Especialidad</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Pago</th> <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->rowCount() > 0): ?>
                        <?php while ($cita = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php 
                                // Color del Estado de la Cita
                                $badgeColor = match($cita['estado']) {
                                    'Pendiente' => 'bg-warning text-dark',
                                    'Finalizada' => 'bg-success',
                                    'Cancelada' => 'bg-danger',
                                    default => 'bg-secondary'
                                };

                                // Lógica visual de Pago
                                // Si id_pago tiene valor, significa que ya existe registro en pagos
                                $estaPagado = !empty($cita['id_pago']); 
                                $badgePago = $estaPagado 
                                    ? '<span class="badge bg-success bg-opacity-75"><i class="fas fa-check me-1"></i>Pagado</span>' 
                                    : '<span class="badge bg-danger bg-opacity-75"><i class="fas fa-times me-1"></i>Pendiente</span>';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?></div>
                                    <div class="small text-muted"><?php echo date('H:i A', strtotime($cita['fecha_cita'])); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"><?php echo $cita['paciente']; ?></span>
                                        <small class="text-muted"><?php echo $cita['paciente_telefono']; ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-primary"><?php echo $cita['medico']; ?></span>
                                        <small class="text-muted"><?php echo $cita['especialidad']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $cita['motivo']; ?></td>
                                <td><span class="badge <?php echo $badgeColor; ?>"><?php echo $cita['estado']; ?></span></td>
                                
                                <td><?php echo $badgePago; ?></td>

                                <td class="text-end pe-4">
                                    <?php if($cita['estado'] == 'Pendiente' || $cita['estado'] == 'Finalizada'): ?>
                                        
                                        <?php if(!$estaPagado): ?>
                                            <a href="<?php echo BASE_URL; ?>/citas/cobrar?id=<?php echo $cita['id_cita']; ?>" class="btn btn-sm btn-outline-success" title="Cobrar">
                                                <i class="fas fa-cash-register"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success disabled border-0" title="Cobro Realizado"><i class="fas fa-check-double"></i></button>
                                        <?php endif; ?>

                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAtencion" 
                                                onclick="cargarAtencion(<?php echo $cita['id_cita']; ?>)"><i class="fas fa-stethoscope"></i></button>
                                        
                                        <?php if($cita['estado'] == 'Pendiente'): ?>
                                            <a href="<?php echo BASE_URL; ?>/citas/eliminar?id=<?php echo $cita['id_cita']; ?>" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('¿Cancelar cita?')"><i class="fas fa-times"></i></a>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron citas en este rango de fechas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agendar Nueva Cita</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/citas/guardar" method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Paciente</label>
                            <select name="id_paciente" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php 
                                if($pacientes) {
                                    $pacientes->execute();
                                    while($p = $pacientes->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='".$p['id_usuario']."'>".$p['nombre']." - ".$p['documento_identidad']."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Servicio</label>
                            <select name="id_servicio" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php 
                                if($servicios) {
                                    $servicios->execute();
                                    while($s = $servicios->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='".$s['id_servicio']."'>".$s['nombre_servicio']." (S/ ".$s['precio'].")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Médico</label>
                            <select name="id_medico" id="selectMedico" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php 
                                if($medicos) {
                                    $medicos->execute();
                                    while($m = $medicos->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='".$m['id_medico']."'>".$m['nombre']." (".$m['especialidad'].")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" id="inputFecha" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora" class="form-control" required>
                        </div>
                    </div>

                    <div id="infoHorarios" class="mb-3 p-3 bg-light rounded border" style="display:none;">
                        <label class="form-label fw-bold text-secondary mb-2"><i class="fas fa-history me-1"></i> Horarios ya reservados para este día:</label>
                        <div id="listaHoras" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motivo de Consulta</label>
                        <textarea name="motivo" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAtencion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Finalizar Atención Médica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/citas/finalizar" method="POST">
                <input type="hidden" name="id_cita" id="atencion_id_cita">
                <div class="modal-body">
                    <h6 class="text-success border-bottom pb-2">Signos Vitales</h6>
                    <div class="row mb-3">
                        <div class="col-md-3"><label>Peso (kg)</label><input type="text" name="peso" class="form-control"></div>
                        <div class="col-md-3"><label>Talla (cm)</label><input type="text" name="talla" class="form-control"></div>
                        <div class="col-md-3"><label>Presión</label><input type="text" name="presion" class="form-control" placeholder="120/80"></div>
                        <div class="col-md-3"><label>Temp (°C)</label><input type="text" name="temperatura" class="form-control"></div>
                    </div>
                    <h6 class="text-primary border-bottom pb-2 mt-4">Diagnóstico y Tratamiento</h6>
                    <div class="mb-3">
                        <label class="fw-bold">Diagnóstico</label>
                        <textarea name="diagnostico" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Receta / Prescripción</label>
                        <textarea name="prescripcion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Días de Reposo Médico</label>
                            <input type="number" name="dias_reposo" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Finalizar Consulta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cargarAtencion(id) {
        document.getElementById('atencion_id_cita').value = id;
    }

    // --- SCRIPT AJAX PARA VER DISPONIBILIDAD ---
    document.addEventListener("DOMContentLoaded", function() {
        const selectMedico = document.getElementById('selectMedico');
        const inputFecha = document.getElementById('inputFecha');
        const infoHorarios = document.getElementById('infoHorarios');
        const listaHoras = document.getElementById('listaHoras');

        function consultarHorarios() {
            const idMedico = selectMedico.value;
            const fecha = inputFecha.value;

            if(idMedico && fecha) {
                fetch(`<?php echo BASE_URL; ?>/citas/verificarHorarios?id_medico=${idMedico}&fecha=${fecha}`)
                    .then(response => response.json())
                    .then(data => {
                        listaHoras.innerHTML = ''; 
                        infoHorarios.style.display = 'block'; 
                        
                        if(data.length > 0) {
                            data.forEach(cita => {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-danger bg-opacity-75 text-white p-2 fw-normal';
                                badge.innerHTML = `<i class="fas fa-clock me-1"></i> ${cita.hora}`;
                                listaHoras.appendChild(badge);
                            });
                        } else {
                            listaHoras.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Todo el día disponible</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error AJAX:', error);
                        infoHorarios.style.display = 'none';
                    });
            } else {
                infoHorarios.style.display = 'none';
            }
        }

        if(selectMedico) selectMedico.addEventListener('change', consultarHorarios);
        if(inputFecha) inputFecha.addEventListener('change', consultarHorarios);
    });

    // Alertas
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg) {
        let data = { title: 'Notificación', icon: 'info', text: 'Acción realizada' };
        if(msg === 'creado') data = { title: 'Cita Agendada', icon: 'success', text: 'La cita se registró correctamente.' };
        else if(msg === 'atendido') data = { title: 'Atención Finalizada', icon: 'success', text: 'Historia clínica actualizada.' };
        else if(msg === 'eliminado') data = { title: 'Cita Cancelada', icon: 'warning', text: 'La cita ha sido anulada.' };
        else if(msg === 'ocupado') data = { title: 'Horario Ocupado', icon: 'error', text: 'El médico ya tiene una cita a esa hora.' };
        else if(msg === 'fuera_horario') data = { title: 'Fuera de Horario', icon: 'error', text: 'El médico no atiende ese día o a esa hora.' };
        
        Swal.fire({ title: data.title, text: data.text, icon: data.icon, confirmButtonColor: '#0d6efd' })
            .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    }
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
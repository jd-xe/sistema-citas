<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Gestión de Horarios</h4>
        <p class="text-muted mb-0">Médico: <strong class="text-primary"><?php echo $medico['nombre']; ?></strong> | Especialidad: <?php echo $medico['especialidad']; ?></p>
    </div>
    <a href="<?php echo BASE_URL; ?>/medicos" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Volver</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-plus-circle me-2"></i> Agregar Turno
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/medicos/guardarHorario" method="POST">
                    <input type="hidden" name="id_medico" value="<?php echo $_GET['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Día de la Semana</label>
                        <select name="dia" class="form-select" required>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                            <option value="Domingo">Domingo</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Inicio</label>
                            <input type="time" name="inicio" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Fin</label>
                            <input type="time" name="fin" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold text-secondary">
                <i class="fas fa-calendar-alt me-2"></i> Horarios Asignados
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Día</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($horarios)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i> No hay horarios registrados para este médico.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($horarios as $h): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?php echo $h['dia_semana']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($h['hora_inicio'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($h['hora_fin'])); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>/medicos/eliminarHorario?id=<?php echo $h['id_horario']; ?>&id_medico=<?php echo $_GET['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger border-0"
                                           onclick="return confirm('¿Eliminar este horario?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
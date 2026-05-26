<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="card shadow border-0">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2"></i> Bitácora de Seguridad (Auditoría)</h5>
    </div>
    <div class="card-body p-4">
        
        <div class="alert alert-info border-0 d-flex align-items-center mb-4">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <strong>Monitoreo de Actividad:</strong>
                Aquí puedes ver todas las acciones realizadas por los usuarios dentro del sistema para control y seguridad.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-sm" id="tablaPro">
                <thead class="table-light">
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($logs->rowCount() > 0): ?>
                        <?php while($row = $logs->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="small text-muted">
                                <?php echo date('d/m/Y H:i:s', strtotime($row['fecha_hora'])); ?>
                            </td>
                            <td class="fw-bold text-primary">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $row['usuario']; ?>
                            </td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    if($row['accion'] == 'CREAR') $badge = 'bg-success';
                                    if($row['accion'] == 'ACTUALIZAR') $badge = 'bg-warning text-dark';
                                    if($row['accion'] == 'ELIMINAR') $badge = 'bg-danger';
                                    if($row['accion'] == 'LOGIN') $badge = 'bg-info text-dark';
                                    if($row['accion'] == 'PAGO') $badge = 'bg-success';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $row['accion']; ?></span>
                            </td>
                            <td class="text-uppercase small fw-bold"><?php echo $row['tabla_afectada']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td class="small font-monospace"><?php echo $row['ip_usuario']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
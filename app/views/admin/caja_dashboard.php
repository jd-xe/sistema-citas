<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Control de Caja</h4>
        <small class="text-muted">Apertura: <?php echo date('d/m/Y h:i A', strtotime($sesionActiva['fecha_apertura'])); ?></small>
    </div>
    <div>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalCierre">
            <i class="fas fa-lock me-2"></i> Cerrar Caja
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body">
                <h6 class="opacity-75">Saldo Inicial</h6>
                <h3 class="fw-bold">S/ <?php echo number_format($saldoInicial, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body">
                <h6 class="opacity-75">Ingresos (Ventas)</h6>
                <h3 class="fw-bold">+ S/ <?php echo number_format($totalIngresos, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body">
                <h6 class="opacity-75">Gastos / Salidas</h6>
                <h3 class="fw-bold">- S/ <?php echo number_format($totalGastos, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white border-start border-5 border-info h-100">
            <div class="card-body text-end">
                <h6 class="text-muted">En Caja (Teórico)</h6>
                <h2 class="fw-bold text-dark">S/ <?php echo number_format($saldoActual, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-success">Últimos Ingresos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light"><tr><th>Paciente</th><th>Servicio</th><th class="text-end">Monto</th></tr></thead>
                        <tbody>
                            <?php if(empty($ingresosRecientes)): ?>
                                <tr><td colspan="3" class="text-center text-muted p-3">Sin movimientos recientes.</td></tr>
                            <?php else: ?>
                                <?php foreach($ingresosRecientes as $ing): ?>
                                    <tr>
                                        <td><?php echo $ing['paciente']; ?></td>
                                        <td><?php echo $ing['nombre_servicio']; ?></td>
                                        <td class="text-end fw-bold text-success">+ S/ <?php echo number_format($ing['monto'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-danger">Gastos Registrados</h5>
                <button class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalGasto">
                    <i class="fas fa-minus me-1"></i> Nuevo Gasto
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light"><tr><th>Descripción</th><th class="text-end">Monto</th></tr></thead>
                        <tbody>
                            <?php if(empty($gastos)): ?>
                                <tr><td colspan="2" class="text-center text-muted p-3">No hay gastos.</td></tr>
                            <?php else: ?>
                                <?php foreach($gastos as $g): ?>
                                    <tr>
                                        <td><?php echo $g['descripcion']; ?></td>
                                        <td class="text-end fw-bold text-danger">- S/ <?php echo number_format($g['monto'], 2); ?></td>
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

<div class="modal fade" id="modalCobro" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-cash-register me-2"></i> Registrar Cobro</h5>
                <a href="<?php echo BASE_URL; ?>/caja" class="btn-close btn-close-white"></a>
            </div>
            <form action="<?php echo BASE_URL; ?>/caja/guardarCobro" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_cita" value="<?php echo $datosCobro['id_cita'] ?? ''; ?>">
                    
                    <div class="alert alert-light border d-flex align-items-center mb-3">
                        <i class="fas fa-user-injured fa-2x text-success me-3"></i>
                        <div>
                            <small class="text-muted d-block">Paciente</small>
                            <h6 class="fw-bold mb-0"><?php echo $datosCobro['paciente'] ?? '---'; ?></h6>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicio</label>
                        <input type="text" class="form-control bg-light" value="<?php echo $datosCobro['nombre_servicio'] ?? ''; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-success">Monto a Cobrar (S/)</label>
                        <input type="number" step="0.01" name="monto" class="form-control form-control-lg fw-bold text-center border-success" 
                               value="<?php echo $datosCobro['precio'] ?? '0.00'; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Método de Pago</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="Yape/Plin">Yape / Plin</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo BASE_URL; ?>/caja" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4">Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGasto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">Registrar Salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/caja/registrarGasto" method="POST">
                <input type="hidden" name="id_sesion" value="<?php echo $sesionActiva['id_sesion']; ?>">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Descripción</label><input type="text" name="descripcion" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Monto (S/)</label><input type="number" step="0.01" name="monto" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark">Guardar</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCierre" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cierre de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/caja/cerrar" method="POST">
                <input type="hidden" name="id_sesion" value="<?php echo $sesionActiva['id_sesion']; ?>">
                <div class="modal-body">
                    <div class="alert alert-info text-center">Saldo Esperado: <strong>S/ <?php echo number_format($saldoActual, 2); ?></strong></div>
                    <div class="mb-3"><label class="form-label">Monto Real</label><input type="number" step="0.01" name="monto_cierre" class="form-control text-center fw-bold" required></div>
                    <div class="mb-3"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-danger w-100">Cerrar Turno</button></div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Abrir modal si hay datos de cobro
        <?php if ($datosCobro): ?>
            var modalCobro = new bootstrap.Modal(document.getElementById('modalCobro'));
            modalCobro.show();
        <?php endif; ?>
    });

    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg) {
        let text = 'Operación Exitosa';
        let icon = 'success';
        if(msg === 'cobro_ok') text = 'Cobro registrado correctamente.';
        else if(msg === 'error') { text = 'Ocurrió un error.'; icon = 'error'; }
        Swal.fire({ title: 'Caja', text: text, icon: icon, confirmButtonColor: '#0d6efd' })
            .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    }
</script>
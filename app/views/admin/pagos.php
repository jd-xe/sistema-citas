<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #2c3e50, #4ca1af);">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="mb-0 text-white-50">Total Recaudado (Selección)</h6>
                    <h2 class="mb-0 fw-bold" id="totalMonto">S/. 0.00</h2>
                </div>
                <i class="fas fa-cash-register fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-bold text-secondary"><i class="fas fa-file-invoice-dollar me-2"></i> Historial de Pagos</h5>
        
        <form action="<?php echo BASE_URL; ?>/pagos" method="GET" class="d-flex gap-2">
            <input type="date" name="inicio" class="form-control form-control-sm" value="<?php echo $fechaInicio; ?>" required>
            <input type="date" name="fin" class="form-control form-control-sm" value="<?php echo $fechaFin; ?>" required>
            <button type="submit" class="btn btn-dark btn-sm px-3"><i class="fas fa-filter"></i> Filtrar</button>
        </form>
    </div>
    
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaPro">
                <thead class="bg-light">
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Método</th>
                        <th>Obs.</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    if($resultado->rowCount() > 0): 
                        while($row = $resultado->fetch(PDO::FETCH_ASSOC)): 
                            $total += $row['monto'];
                    ?>
                        <tr>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['fecha_pago'])); ?> 
                                <small class="text-muted"><?php echo date('H:i', strtotime($row['fecha_pago'])); ?></small>
                            </td>
                            <td class="fw-bold text-dark"><?php echo $row['paciente']; ?></td>
                            <td><?php echo $row['nombre_servicio'] ? $row['nombre_servicio'] : 'Consulta General'; ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo $row['metodo_pago']; ?>
                                </span>
                            </td>
                            <td class="small text-muted fst-italic"><?php echo $row['observaciones']; ?></td>
                            <td class="text-end fw-bold text-success">
                                <?php echo $empresa['moneda'] . ' ' . number_format($row['monto'], 2); ?>
                            </td>
                            <td class="text-center">
                                <a href="#" onclick="confirmarEliminacion(<?php echo $row['id_pago']; ?>)" class="btn btn-sm btn-outline-danger border-0" title="Anular Pago">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Pasar el total calculado por PHP al JavaScript para mostrarlo en la tarjeta de arriba
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('totalMonto').innerText = "<?php echo $empresa['moneda'] . ' ' . number_format($total, 2); ?>";
    });

    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Anular Pago?',
            text: "El registro de dinero será eliminado. Esto afecta el reporte de caja.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo BASE_URL; ?>/pagos/eliminar?id=" + id;
            }
        })
    }
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
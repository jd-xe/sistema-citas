<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="card shadow border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-pills me-2"></i> Farmacia / Medicamentos</h5>
        
        <?php if($_SESSION['user_role_id'] == 1): // Solo Admin crea ?>
        <button class="btn btn-light text-primary fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="fas fa-plus me-2"></i> Nuevo Medicamento
        </button>
        <?php endif; ?>
    </div>
    
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaPro">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Nombre Comercial</th>
                        <th>Genérico</th>
                        <th>Presentación</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Estado</th>
                        <?php if($_SESSION['user_role_id'] == 1): ?>
                        <th class="text-center">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if($resultado->rowCount() > 0): ?>
                        <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark"><?php echo $row['nombre_comercial']; ?></td>
                            <td class="text-muted"><?php echo $row['nombre_generico']; ?></td>
                            <td><?php echo $row['presentacion']; ?></td>
                            <td class="text-center">
                                <?php if($row['stock'] > 10): ?>
                                    <span class="badge bg-success rounded-pill"><?php echo $row['stock']; ?></span>
                                <?php elseif($row['stock'] > 0): ?>
                                    <span class="badge bg-warning text-dark rounded-pill"><?php echo $row['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill">Agotado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['estado'] == 'Activo'): ?>
                                    <span class="badge bg-soft-success text-success border border-success bg-opacity-10">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if($_SESSION['user_role_id'] == 1): ?>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary border-0 me-1" 
                                        data-bs-toggle="modal" data-bs-target="#modalEditar"
                                        onclick="cargarDatos('<?php echo $row['id_medicamento']; ?>', '<?php echo $row['nombre_comercial']; ?>', '<?php echo $row['nombre_generico']; ?>', '<?php echo $row['presentacion']; ?>', '<?php echo $row['stock']; ?>', '<?php echo $row['estado']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="#" onclick="confirmarEliminacion(<?php echo $row['id_medicamento']; ?>)" class="btn btn-sm btn-outline-danger border-0">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Medicamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/medicamentos/guardar" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold">Nombre Comercial</label>
                        <input type="text" name="nombre_comercial" class="form-control" required placeholder="Ej: Panadol">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nombre Genérico</label>
                        <input type="text" name="nombre_generico" class="form-control" placeholder="Ej: Paracetamol">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Presentación</label>
                            <input type="text" name="presentacion" class="form-control" placeholder="Ej: Caja 100mg">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Stock Inicial</label>
                            <input type="number" name="stock" class="form-control" value="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Editar Medicamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/medicamentos/actualizar" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_medicamento" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="fw-bold">Estado</label>
                        <select name="estado" id="edit_estado" class="form-select border-warning">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nombre Comercial</label>
                        <input type="text" name="nombre_comercial" id="edit_comercial" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nombre Genérico</label>
                        <input type="text" name="nombre_generico" id="edit_generico" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Presentación</label>
                            <input type="text" name="presentacion" id="edit_presentacion" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Stock</label>
                            <input type="number" name="stock" id="edit_stock" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cargarDatos(id, comercial, generico, presentacion, stock, estado) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_comercial').value = comercial;
        document.getElementById('edit_generico').value = generico;
        document.getElementById('edit_presentacion').value = presentacion;
        document.getElementById('edit_stock').value = stock;
        document.getElementById('edit_estado').value = estado;
    }

    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Eliminar?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "<?php echo BASE_URL; ?>/medicamentos/eliminar?id=" + id;
        })
    }
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
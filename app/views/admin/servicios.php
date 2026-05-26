<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="card shadow border-0 mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-tags me-2"></i> Tarifario de Servicios</h5>
        <button class="btn btn-light text-dark fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalServicio">
            <i class="fas fa-plus me-2"></i> Nuevo Servicio
        </button>
    </div>
    
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaPro">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Nombre del Servicio</th>
                        <th>Descripción</th>
                        <th class="text-center">Precio Unitario</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($resultado->rowCount() > 0): ?>
                        <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark"><?php echo $row['nombre_servicio']; ?></td>
                            <td class="text-muted small"><?php echo $row['descripcion']; ?></td>
                            <td class="text-center fw-bold fs-5 text-success">
                                <?php echo $empresa['moneda'] . ' ' . number_format($row['precio'], 2); ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['estado'] == 'Activo'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary border-0 me-1" 
                                        data-bs-toggle="modal" data-bs-target="#modalEditar"
                                        onclick="cargarDatos(
                                            '<?php echo $row['id_servicio']; ?>',
                                            '<?php echo $row['nombre_servicio']; ?>',
                                            '<?php echo $row['descripcion']; ?>',
                                            '<?php echo $row['precio']; ?>',
                                            '<?php echo $row['estado']; ?>'
                                        )">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>/servicios/eliminar?id=<?php echo $row['id_servicio']; ?>" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Nuevo Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/servicios/guardar" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold">Nombre del Servicio</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Consulta General">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Precio (<?php echo $empresa['moneda']; ?>)</label>
                        <input type="number" step="0.01" name="precio" class="form-control form-control-lg text-end" required placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Guardar Servicio</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Editar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/servicios/actualizar" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_servicio" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold">Estado</label>
                        <select name="estado" id="edit_estado" class="form-select border-warning">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="fw-bold">Nombre</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="fw-bold">Descripción</label><textarea name="descripcion" id="edit_desc" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Precio</label><input type="number" step="0.01" name="precio" id="edit_precio" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 fw-bold">Actualizar</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    function cargarDatos(id, nombre, desc, precio, estado) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_desc').value = desc;
        document.getElementById('edit_precio').value = precio;
        document.getElementById('edit_estado').value = estado;
    }
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
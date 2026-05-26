<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-secondary fw-bold">Catálogo de Especialidades</h2>
    <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEspecialidad">
        <i class="fas fa-plus me-2"></i> Nueva Especialidad
    </button>
</div>

<div class="card shadow border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaEspecialidades" width="100%">
                <thead class="bg-light">
                    <tr>
                        <th>Nombre de Especialidad</th>
                        <th class="text-center">Médicos Asignados</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php 
                            $id = $row['id_especialidad'];
                            $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES);
                            $total = $row['total_medicos'];
                            
                            // Lógica de Estado
                            $estado = isset($row['estado']) ? (int)$row['estado'] : 1;
                            $badgeClase = $estado == 1 ? 'bg-success' : 'bg-secondary';
                            $textoEstado = $estado == 1 ? 'Activa' : 'Inactiva';
                            $filaClase = $estado == 1 ? '' : 'bg-light text-muted opacity-75';
                            
                            // Botón Toggle
                            $btnIcono = $estado == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                            $btnColor = $estado == 1 ? 'text-success' : 'text-secondary';
                            $btnTitulo = $estado == 1 ? 'Desactivar' : 'Activar';
                        ?>
                        <tr class="<?php echo $filaClase; ?>">
                            <td class="fw-bold ps-3"><?php echo $nombre; ?></td>
                            <td class="text-center">
                                <?php if($total > 0): ?>
                                    <span class="badge bg-primary rounded-pill"><?php echo $total; ?> Doctores</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill bg-opacity-25 text-dark">Sin personal</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badgeClase; ?>"><?php echo $textoEstado; ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary border-0 me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar"
                                        
                                        data-id="<?php echo $id; ?>"
                                        data-nombre="<?php echo $nombre; ?>"
                                        
                                        onclick="llenarModalEditar(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-light border-0 fs-5 <?php echo $btnColor; ?>" 
                                        title="<?php echo $btnTitulo; ?>" 
                                        onclick="confirmarCambioEstado(<?php echo $id; ?>, <?php echo $estado; ?>)">
                                    <i class="fas <?php echo $btnIcono; ?>"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEspecialidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Registrar Especialidad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/especialidades/guardar" method="POST">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold">Nombre de la Especialidad</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej: Cardiología">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Especialidad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/especialidades/actualizar" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_especialidad" id="edit_id">
                    <label class="form-label fw-bold">Nombre de la Especialidad</label>
                    <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#tablaEspecialidades')) {
            $('#tablaEspecialidades').DataTable().destroy();
        }
        $('#tablaEspecialidades').DataTable({
            responsive: true,
            searching: true,
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print'],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
        });
    });

    // LLENAR MODAL EDITAR
    function llenarModalEditar(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_nombre').value = btn.getAttribute('data-nombre');
    }

    // CONFIRMAR CAMBIO DE ESTADO
    function confirmarCambioEstado(id, estadoActual) {
        let accion = (estadoActual == 1) ? "desactivar" : "activar";
        let colorBtn = (estadoActual == 1) ? '#d33' : '#198754';
        
        Swal.fire({
            title: '¿' + accion.charAt(0).toUpperCase() + accion.slice(1) + ' Especialidad?',
            text: "El estado cambiará inmediatamente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: colorBtn,
            confirmButtonText: 'Sí, ' + accion,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo BASE_URL; ?>/especialidades/cambiarEstado?id=" + id + "&estado=" + estadoActual;
            }
        });
    }

    // ALERTAS
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg) {
        let data = { title: 'Acción', icon: 'info', text: 'Realizado' };
        if(msg === 'creado') data = { title: '¡Éxito!', icon: 'success', text: 'Especialidad creada.' };
        else if(msg === 'actualizado') data = { title: '¡Actualizado!', icon: 'success', text: 'Nombre modificado.' };
        else if(msg === 'activado') data = { title: 'Activado', icon: 'success', text: 'Especialidad habilitada.' };
        else if(msg === 'desactivado') data = { title: 'Desactivado', icon: 'warning', text: 'Especialidad inhabilitada.' };
        else if(msg === 'error') data = { title: 'Error', icon: 'error', text: 'Ocurrió un problema.' };
        
        Swal.fire({ title: data.title, text: data.text, icon: data.icon, confirmButtonColor: '#0d6efd' })
            .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    }
</script>
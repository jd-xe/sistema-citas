<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-secondary fw-bold">Gestión de Médicos</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMedico">
        <i class="fas fa-plus me-2"></i> Nuevo Médico
    </button>
</div>

<div class="card shadow border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaMedicos" width="100%">
                <thead class="bg-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Email / Colegiatura</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $medicos->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php 
                            // Sanitización de datos
                            $id_medico = $row['id_medico'];
                            $id_usuario = $row['id_usuario'];
                            $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES);
                            $email = htmlspecialchars($row['email'] ?? '', ENT_QUOTES);
                            $colegiatura = htmlspecialchars($row['colegiatura'] ?? '', ENT_QUOTES);
                            $especialidad = htmlspecialchars($row['especialidad'] ?? '', ENT_QUOTES);
                            $id_especialidad = $row['id_especialidad'];

                            // Estado
                            $estado = isset($row['estado']) ? (int)$row['estado'] : 1;
                            $badgeClase = $estado == 1 ? 'bg-success' : 'bg-secondary';
                            $textoEstado = $estado == 1 ? 'Activo' : 'Inactivo';
                            $filaClase = $estado == 1 ? '' : 'bg-light text-muted opacity-75';
                            
                            // Botón Toggle
                            $btnIcono = $estado == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                            $btnColor = $estado == 1 ? 'text-success' : 'text-secondary';
                            $btnTitulo = $estado == 1 ? 'Desactivar' : 'Activar';
                        ?>
                        <tr class="<?php echo $filaClase; ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <span class="fw-bold"><?php echo $nombre; ?></span>
                                </div>
                            </td>
                            <td><span class="badge bg-outline-success text-success border border-success"><?php echo $especialidad; ?></span></td>
                            <td>
                                <div class="small text-muted"><?php echo $email; ?></div>
                                <div class="small text-secondary">Col: <?php echo $colegiatura; ?></div>
                            </td>
                            <td><span class="badge <?php echo $badgeClase; ?>"><?php echo $textoEstado; ?></span></td>
                            <td class="text-end">
                                <a href="<?php echo BASE_URL; ?>/medicos/horarios?id=<?php echo $id_medico; ?>" class="btn btn-sm btn-info text-white me-1" title="Gestionar Horarios">
                                    <i class="fas fa-clock"></i> Horarios
                                </a>

                                <button class="btn btn-sm btn-primary me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar"
                                        
                                        data-idmedico="<?php echo $id_medico; ?>"
                                        data-idusuario="<?php echo $id_usuario; ?>"
                                        data-nombre="<?php echo $nombre; ?>"
                                        data-email="<?php echo $email; ?>"
                                        data-colegiatura="<?php echo $colegiatura; ?>"
                                        data-idespecialidad="<?php echo $id_especialidad; ?>"
                                        
                                        onclick="llenarModalEditar(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-light border-0 fs-5 <?php echo $btnColor; ?>" 
                                        title="<?php echo $btnTitulo; ?>"
                                        onclick="cambiarEstado(<?php echo $id_usuario; ?>, <?php echo $estado; ?>)">
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

<div class="modal fade" id="modalMedico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar Nuevo Médico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/medicos/guardar" method="POST">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre Completo</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Especialidad</label>
                        <select name="id_especialidad" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php 
                            // Reiniciamos puntero de especialidades
                            $especialidades->execute(); 
                            while($esp = $especialidades->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $esp['id_especialidad']; ?>"><?php echo $esp['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Nro Colegiatura</label><input type="text" name="colegiatura" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Correo Electrónico</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Contraseña</label><input type="password" name="password" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">Guardar</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Médico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/medicos/actualizar" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_medico" id="edit_id_medico">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <select name="id_especialidad" id="edit_especialidad" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php 
                            $especialidades->execute(); 
                            while($esp = $especialidades->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $esp['id_especialidad']; ?>"><?php echo $esp['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nro Colegiatura</label>
                        <input type="text" name="colegiatura" id="edit_colegiatura" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Actualizar</button></div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#tablaMedicos')) {
            $('#tablaMedicos').DataTable().destroy();
        }
        $('#tablaMedicos').DataTable({
            responsive: true,
            searching: true,
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print'],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
        });
    });

    // LLENAR EL MODAL DE EDICIÓN
    function llenarModalEditar(btn) {
        // Usamos los data-attributes para evitar errores
        document.getElementById('edit_id_medico').value = btn.getAttribute('data-idmedico');
        document.getElementById('edit_id_usuario').value = btn.getAttribute('data-idusuario');
        document.getElementById('edit_nombre').value = btn.getAttribute('data-nombre');
        document.getElementById('edit_email').value = btn.getAttribute('data-email');
        document.getElementById('edit_colegiatura').value = btn.getAttribute('data-colegiatura');
        document.getElementById('edit_especialidad').value = btn.getAttribute('data-idespecialidad');
    }

    // ACTIVAR / DESACTIVAR
    function cambiarEstado(id, estadoActual) {
        let accion = (estadoActual == 1) ? "desactivar" : "activar";
        let colorBtn = (estadoActual == 1) ? '#d33' : '#198754';
        
        Swal.fire({
            title: '¿' + accion.charAt(0).toUpperCase() + accion.slice(1) + ' Médico?',
            text: "El médico perderá o recuperará el acceso al sistema.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: colorBtn,
            confirmButtonText: 'Sí, ' + accion,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo BASE_URL; ?>/medicos/cambiarEstado?id=" + id + "&estado=" + estadoActual;
            }
        });
    }

    // Alertas
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg) {
        let title = 'Acción', icon = 'info', text = 'Realizado';
        if(msg === 'creado') { title = '¡Registrado!'; icon = 'success'; text = 'Médico agregado.'; }
        else if(msg === 'actualizado') { title = '¡Actualizado!'; icon = 'success'; text = 'Datos guardados.'; }
        else if(msg === 'activado') { title = 'Activado'; icon = 'success'; text = 'Acceso habilitado.'; }
        else if(msg === 'desactivado') { title = 'Desactivado'; icon = 'warning'; text = 'Acceso inhabilitado.'; }
        else if(msg === 'error') { title = 'Error'; icon = 'error'; text = 'Ocurrió un problema.'; }
        
        Swal.fire({ title: title, text: text, icon: icon, confirmButtonColor: '#0d6efd' })
            .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    }
</script>
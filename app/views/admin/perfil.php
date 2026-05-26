<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white py-3 text-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-id-card me-2"></i> Mi Perfil Profesional</h5>
            </div>
            <div class="card-body p-4">
                
                <form action="<?php echo BASE_URL; ?>/perfil/actualizar" method="POST" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <?php if(!empty($usuario['avatar'])): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $usuario['avatar']; ?>" class="rounded-circle shadow border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle shadow d-flex align-items-center justify-content-center text-secondary" style="width: 120px; height: 120px; font-size: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            
                            <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle p-2 shadow" style="cursor: pointer; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-camera small"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/jpeg, image/png">
                        </div>
                        <h4 class="mt-3 fw-bold"><?php echo $usuario['nombre']; ?></h4>
                        <span class="badge bg-info text-dark"><?php echo $usuario['rol_nombre']; ?></span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                            <input type="text" class="form-control bg-light" value="<?php echo $usuario['email']; ?>" readonly>
                        </div>
                        <div class="form-text text-muted"><small>El correo no se puede modificar.</small></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
                            <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
                        </div>
                    </div>

                    <div class="accordion mt-4" id="accordionPass">
                        <div class="accordion-item border-0 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePass">
                                    <i class="fas fa-key me-2"></i> Cambiar Contraseña
                                </button>
                            </h2>
                            <div id="collapsePass" class="accordion-collapse collapse" data-bs-parent="#accordionPass">
                                <div class="accordion-body bg-light">
                                    <div class="mb-2">
                                        <label class="small fw-bold">Nueva Contraseña</label>
                                        <input type="password" name="password_nueva" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-0">
                                        <label class="small fw-bold">Confirmar</label>
                                        <input type="password" name="password_confirmar" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-warning border-0 d-flex align-items-center">
                        <i class="fas fa-lock me-3 fs-4"></i>
                        <div>
                            <label class="fw-bold small">Contraseña ACTUAL (Requerido para guardar)</label>
                            <input type="password" name="password_actual" class="form-control" required placeholder="Ingresa tu clave actual...">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">Guardar Cambios</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Previsualizar imagen antes de subir
    document.getElementById('avatarInput').onchange = function (evt) {
        var tgt = evt.target || window.event.srcElement, files = tgt.files;
        if (FileReader && files && files.length) {
            var fr = new FileReader();
            fr.onload = function () {
                // Buscar la imagen y cambiar su src
                document.querySelector('.rounded-circle.shadow').src = fr.result;
            }
            fr.readAsDataURL(files[0]);
        }
    }

    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg === 'guardado') Swal.fire('¡Actualizado!', 'Tu perfil se ha guardado correctamente.', 'success').then(() => window.history.replaceState({}, document.title, window.location.pathname));
    if(msg === 'error_pass') Swal.fire('Error', 'La contraseña actual es incorrecta.', 'error');
    if(msg === 'no_match') Swal.fire('Error', 'Las nuevas contraseñas no coinciden.', 'error');
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
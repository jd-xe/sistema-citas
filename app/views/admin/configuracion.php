<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cogs me-2"></i> Configuración del Sistema</h5>
            </div>
            <div class="card-body p-5">
                
                <form action="<?php echo BASE_URL; ?>/configuracion/guardar" method="POST" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold d-block">Logotipo de la Clínica</label>
                        <div class="mb-3">
                            <?php if(!empty($datos['logo'])): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $datos['logo']; ?>?t=<?php echo time(); ?>" alt="Logo Actual" class="img-thumbnail" style="max-height: 100px;">
                            <?php else: ?>
                                <div class="badge bg-secondary p-3">Sin Logo</div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="logo" class="form-control w-75 mx-auto" accept="image/png, image/jpeg">
                        <div class="form-text">Recomendado: PNG transparente (300x100 px).</div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nombre de la Clínica</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" value="<?php echo $datos['nombre_clinica']; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dirección Física</label>
                            <input type="text" name="direccion" class="form-control" value="<?php echo $datos['direccion']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono de Contacto</label>
                            <input type="text" name="telefono" class="form-control" value="<?php echo $datos['telefono']; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $datos['email']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Símbolo de Moneda</label>
                            <input type="text" name="moneda" class="form-control" value="<?php echo $datos['moneda']; ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg === 'guardado') Swal.fire('Guardado', 'La configuración se actualizó correctamente', 'success')
        .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    if(msg === 'error') Swal.fire('Error', 'No se pudo guardar la configuración', 'error');
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
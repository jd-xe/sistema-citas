<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-8 col-lg-6">
        
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-danger text-white text-center py-3">
                <h3 class="fw-bold mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Zona de Peligro</h3>
            </div>
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="text-danger fw-bold">Restablecer Sistema</h2>
                    <p class="text-muted">Esta acción dejará la base de datos como nueva para pruebas.</p>
                </div>

                <div class="alert alert-warning border-warning">
                    <h5 class="alert-heading fw-bold"><i class="fas fa-info-circle"></i> ¿Qué sucederá?</h5>
                    <ul class="mb-0 small">
                        <li>Se eliminarán <strong>TODOS</strong> los Pacientes y Médicos.</li>
                        <li>Se eliminarán <strong>TODAS</strong> las Citas y Expedientes.</li>
                        <li>Se vaciará la Caja, Pagos y Gastos.</li>
                        <li>Se borrarán Especialidades y Servicios.</li>
                        <li><strong>SOLO SE CONSERVARÁN:</strong> Usuarios Administradores y Configuración.</li>
                    </ul>
                </div>

                <form action="<?php echo BASE_URL; ?>/sistema/ejecutarReset" method="POST" id="formReset">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirme su contraseña de Administrador:</label>
                        <input type="password" name="password" class="form-control form-control-lg text-center" placeholder="Ingrese su contraseña" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger btn-lg fw-bold" onclick="confirmarReset()">
                            <i class="fas fa-trash-alt me-2"></i> CONFIRMAR Y BORRAR TODO
                        </button>
                        <a href="<?php echo BASE_URL; ?>/home" class="btn btn-secondary btn-lg">
                            Cancelar y Salir
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script>
    function confirmarReset() {
        Swal.fire({
            title: '¿ESTÁS ABSOLUTAMENTE SEGURO?',
            text: "No podrás revertir esto. Toda la información operativa será eliminada permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, borrar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formReset').submit();
            }
        })
    }

    // Alertas de respuesta
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if(msg === 'error_pass') {
        Swal.fire('Error', 'La contraseña ingresada es incorrecta.', 'error');
    } else if(msg === 'error_db') {
        Swal.fire('Error', 'Ocurrió un error en la base de datos.', 'error');
    }
</script>
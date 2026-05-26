<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow-lg border-0 text-center p-5" style="width: 100%; max-width: 500px;">
        <div class="mb-4">
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex p-4">
                <i class="fas fa-cash-register fa-4x"></i>
            </div>
        </div>
        <h2 class="fw-bold text-secondary mb-3">Apertura de Caja</h2>
        <p class="text-muted mb-4">La caja se encuentra cerrada. Ingrese el monto inicial para comenzar a operar.</p>
        
        <form action="<?php echo BASE_URL; ?>/caja/abrir" method="POST">
            <div class="form-floating mb-4">
                <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-center text-success" id="monto" name="monto_apertura" placeholder="0.00" required>
                <label for="monto">Monto Inicial (S/)</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                <i class="fas fa-unlock me-2"></i> Abrir Turno
            </button>
        </form>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
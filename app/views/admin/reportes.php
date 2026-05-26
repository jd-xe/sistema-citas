<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h3 class="fw-bold text-dark"><i class="fas fa-chart-line me-2"></i> Reportes Gerenciales</h3>
    <button onclick="window.print()" class="btn btn-outline-dark"><i class="fas fa-print me-2"></i> Imprimir Informe</button>
</div>

<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body py-3">
        <form action="<?php echo BASE_URL; ?>/reportes" method="GET" class="row g-2 align-items-center">
            <div class="col-auto fw-bold">Rango de Fechas:</div>
            <div class="col-auto">
                <input type="date" name="inicio" class="form-control" value="<?php echo $fechaInicio; ?>" required>
            </div>
            <div class="col-auto">
                <input type="date" name="fin" class="form-control" value="<?php echo $fechaFin; ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
            <div class="card-body p-4 text-center">
                <h5 class="opacity-75">Ingresos Totales (<?php echo date('d/m', strtotime($fechaInicio)) . ' al ' . date('d/m', strtotime($fechaFin)); ?>)</h5>
                <h1 class="display-4 fw-bold mb-0">
                    <?php echo $empresa['moneda'] . ' ' . number_format($ingresos, 2); ?>
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Estado de Citas</div>
            <div class="card-body">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Top Servicios (Ingresos)</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Servicio</th><th class="text-end">Ventas</th><th class="text-end">Ingresos</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($topServicios as $serv): ?>
                        <tr>
                            <td><?php echo $serv['nombre_servicio']; ?></td>
                            <td class="text-end"><?php echo $serv['cantidad']; ?></td>
                            <td class="text-end fw-bold text-success"><?php echo number_format($serv['ingresos'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($topServicios)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Sin datos en este periodo</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Rendimiento por Médico (Citas Atendidas)</div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Médico</th><th class="text-center">Total Citas</th><th style="width: 50%;">Barra de Progreso</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $maxCitas = !empty($topMedicos) ? $topMedicos[0]['total_citas'] : 1; 
                        foreach($topMedicos as $med): 
                            $porcentaje = ($med['total_citas'] / $maxCitas) * 100;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $med['medico']; ?></td>
                            <td class="text-center badge bg-primary rounded-pill fs-6 mt-2"><?php echo $med['total_citas']; ?></td>
                            <td>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $porcentaje; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .no-print { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
</style>

<script>
    // Preparar datos para el gráfico circular
    const datosEstados = <?php echo json_encode($estadosCitas); ?>;
    const labels = datosEstados.map(e => e.estado);
    const data = datosEstados.map(e => e.cantidad);
    const colores = ['#ffc107', '#0d6efd', '#198754', '#dc3545']; // Amarillo, Azul, Verde, Rojo

    new Chart(document.getElementById('chartEstados'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colores
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
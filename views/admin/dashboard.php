<div class="container mt-4">
    <h2>Dashboard del Administrador</h2>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people"></i> Usuarios Totales</h5>
                    <h2 class="display-4"><?= $stats['users'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-box"></i> Productos Activos</h5>
                    <h2 class="display-4"><?= $stats['products'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-receipt"></i> Facturas Pendientes</h5>
                    <h2 class="display-4"><?= $stats['pending_sales'] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas (Nuevas) -->
    <div class="row mt-4">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Ventas Generales (Últimos 30 Días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Mejores Vendedores (Mes Actual)</h5>
                </div>
                <div class="card-body">
                    <canvas id="sellersChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data para Gráfico 1
    const salesData = <?= json_encode($chart_sales) ?>;
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: salesData.map(row => row.date),
            datasets: [{
                label: 'Monto Facturado ($)',
                data: salesData.map(row => row.total),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Data para Gráfico 2
    const sellersData = <?= json_encode($chart_sellers) ?>;
    const ctxSellers = document.getElementById('sellersChart').getContext('2d');
    new Chart(ctxSellers, {
        type: 'bar',
        data: {
            labels: sellersData.map(row => row.username),
            datasets: [{
                label: 'Ventas en el Mes ($)',
                data: sellersData.map(row => row.total),
                backgroundColor: '#198754'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>

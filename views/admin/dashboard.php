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
</div>

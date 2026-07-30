<div class="container mt-4">
    <h2 class="mb-4">Panel de Control - Contador</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card bg-primary text-white shadow h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-receipt"></i> Ventas Pendientes de Aprobación</h5>
                    <h2 class="display-4"><?= $pending_sales ?></h2>
                    <p class="mb-0">Facturas que requieren tu revisión.</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?page=accountant_sales" class="btn btn-light btn-sm text-primary fw-bold">Ver Ventas <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-file-earmark-check"></i> DDJJ Pendientes (Admin)</h5>
                    <h2 class="display-4"><?= $pending_ddjj ?></h2>
                    <p class="mb-0">Declaraciones Juradas esperando aprobación del Administrador.</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?page=accountant_ddjj" class="btn btn-light btn-sm text-success fw-bold">Ver Balances <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

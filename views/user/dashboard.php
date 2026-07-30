<div class="container mt-4">
    <h2>Mi Panel (Vendedor)</h2>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Factura enviada al administrador exitosamente.</div>
    <?php endif; ?>

    <div class="mt-4">
        <h4>Mis Últimas Facturas Generadas</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>N° Factura</th>
                                <th>Fecha</th>
                                <th>Monto Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($my_sales as $s): ?>
                            <tr>
                                <td class="fw-bold">#<?= str_pad($s['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                <td><?= $s['created_at'] ?></td>
                                <td class="fw-bold text-success">$<?= number_format($s['total'], 2) ?></td>
                                <td>
                                    <?php if($s['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> En Revisión</span>
                                    <?php elseif($s['status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aprobada</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($my_sales)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Aún no has generado facturas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h2>Mi Panel (Vendedor)</h2>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Factura enviada al administrador exitosamente.</div>
    <?php endif; ?>

    <!-- Nueva sección de Metas -->
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-bullseye"></i> Mi Meta Mensual de Ventas
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Progreso Actual: <strong>$<?= number_format($current_sales, 2) ?></strong></span>
                        <span>Meta: <strong>$<?= number_format($monthly_goal, 2) ?></strong></span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar <?= $goal_percentage >= 100 ? 'bg-success' : 'bg-primary' ?> progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: <?= $goal_percentage ?>%;" 
                             aria-valuenow="<?= $goal_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= number_format($goal_percentage, 1) ?>%
                        </div>
                    </div>
                    <?php if($goal_percentage >= 100 && $monthly_goal > 0): ?>
                        <div class="mt-2 text-success fw-bold"><i class="bi bi-trophy-fill text-warning"></i> ¡Felicidades! Has alcanzado tu meta mensual.</div>
                    <?php elseif($monthly_goal == 0): ?>
                        <div class="mt-2 text-muted">No tienes una meta asignada para este mes.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

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
                                <th class="text-end">Opciones</th>
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
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#saleModal<?= $s['id'] ?>">
                                    <i class="bi bi-eye"></i> Detalles
                                </button>
                                <a href="index.php?page=view_invoice&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>

                                <!-- Detalles Modal -->
                                <div class="modal fade text-start" id="saleModal<?= $s['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title">Detalle de Venta #<?= str_pad($s['id'], 6, '0', STR_PAD_LEFT) ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th class="text-center">Cant.</th>
                                                            <th class="text-end">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($s['details'] as $detail): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($detail['name']) ?></td>
                                                            <td class="text-center"><?= $detail['quantity'] ?></td>
                                                            <td class="text-end">$<?= number_format($detail['subtotal'], 2) ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <h4 class="text-end fw-bold mt-3 text-primary">Total: $<?= number_format($s['total'], 2) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($my_sales)): ?>
                                <tr><td colspan="5" class="text-center text-muted">Aún no has generado facturas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

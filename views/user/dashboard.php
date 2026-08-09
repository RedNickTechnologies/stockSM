<div class="container mt-4">
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Factura enviada al administrador exitosamente.</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mi Panel (Vendedor)</h2>
        <div>
            <span class="text-muted me-3"><i class="bi bi-calendar-check"></i> Ingresó: <?= date('d/m/Y', strtotime($created_at)) ?></span>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#passwordModal">
                <i class="bi bi-key"></i> Cambiar Contraseña
            </button>
        </div>
    </div>

    <?php if(isset($password_msg) && $password_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($password_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Tarjeta de Meta -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-info h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bullseye"></i> Mi Meta Mensual de Ventas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Progreso Actual: <strong>$<?= number_format($current_sales, 2) ?></strong></span>
                        <span>Meta: <strong>$<?= number_format($monthly_goal, 2) ?></strong></span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar <?= $goal_percentage >= 100 ? 'bg-success' : 'bg-primary' ?>" role="progressbar" style="width: <?= $goal_percentage ?>%;" aria-valuenow="<?= $goal_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= number_format($goal_percentage, 1) ?>%
                        </div>
                    </div>
                    <?php if($monthly_goal == 0): ?>
                        <p class="text-muted mt-3 small">No tienes una meta asignada para este mes.</p>
                    <?php elseif($goal_percentage >= 100): ?>
                        <p class="text-success mt-3 small fw-bold"><i class="bi bi-trophy-fill"></i> ¡Felicidades! Has alcanzado tu meta mensual.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Métricas -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Mis Métricas del Mes (Aprobadas)</h5>
                </div>
                <div class="card-body d-flex justify-content-around align-items-center">
                    <div class="text-center">
                        <h2 class="display-5 text-primary"><?= $current_count ?></h2>
                        <span class="text-muted">Ventas Realizadas</span>
                    </div>
                    <div class="text-center">
                        <h2 class="display-5 text-success">$<?= number_format($current_sales, 2) ?></h2>
                        <span class="text-muted">Total Facturado</span>
                    </div>
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
                                <tr><td colspan="5" class="text-center text-muted">Aún no has registrado ninguna venta.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=user_dashboard" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="update_password">
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

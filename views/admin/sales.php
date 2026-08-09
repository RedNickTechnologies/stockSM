<div class="container mt-4">
    <h2 class="mb-4">Validación de Facturas Pendientes</h2>

    <div class="card shadow-sm border-warning">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Factura</th>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                            <th>Tipo Entrega</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $s): ?>
                        <tr class="<?= $s['status'] === 'pending' ? 'table-warning' : '' ?>">
                            <td class="fw-bold">#<?= str_pad($s['id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($s['username']) ?></td>
                            <td><?= $s['created_at'] ?></td>
                            <td class="fw-bold">$<?= number_format($s['total'], 2) ?></td>
                            <td>
                                <?php if($s['delivery_type'] === 'transport'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-truck"></i> Envío Transporte</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-shop"></i> Venta Directa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($s['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pendiente</span>
                                <?php elseif($s['status'] === 'approved'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aprobada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=view_invoice&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" target="_blank" title="Ver Factura PDF">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <?php if($s['status'] === 'pending'): ?>
                                    <form action="index.php?page=admin_sales" method="POST" class="d-inline mb-1">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                        <?php if($s['delivery_type'] === 'transport'): ?>
                                            <div class="input-group input-group-sm mb-1" style="max-width: 250px;">
                                                <select name="transporter_id" class="form-select" required>
                                                    <option value="">Asignar a...</option>
                                                    <?php foreach($transporters as $t): ?>
                                                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['username']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar factura y asignar transportista?');">
                                                    <i class="bi bi-check-lg"></i> Aprobar
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Aprobar factura de venta directa?');">
                                                <i class="bi bi-check-lg"></i> Aprobar
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    <form action="index.php?page=admin_sales" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Rechazar factura? (NO se descontará stock)');">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled><i class="bi bi-check-all"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay facturas registradas.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

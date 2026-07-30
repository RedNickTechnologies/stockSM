<div class="container mt-4">
    <h2 class="mb-4">Validación de Balances y Declaraciones Juradas</h2>

    <div class="card shadow-sm border-info">
        <div class="card-header bg-info text-dark">
            <h5 class="mb-0"><i class="bi bi-file-earmark-bar-graph"></i> DDJJs generadas por Contaduría</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Período</th>
                            <th>Contador</th>
                            <th>Total Facturado</th>
                            <th>IVA Estimado</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ddjjs as $d): ?>
                        <tr>
                            <td class="fw-bold">#<?= $d['id'] ?></td>
                            <td><?= str_pad($d['month'], 2, '0', STR_PAD_LEFT) ?>/<?= $d['year'] ?></td>
                            <td><?= htmlspecialchars($d['accountant_name']) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($d['total_sales'], 2) ?></td>
                            <td class="fw-bold text-danger">$<?= number_format($d['total_taxes'], 2) ?></td>
                            <td>
                                <?php if($d['status'] === 'pending_admin'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Esperando tu Aprobación</span>
                                <?php elseif($d['status'] === 'approved'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aprobada</span>
                                <?php elseif($d['status'] === 'sent_to_arca'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-cloud-check"></i> Presentada en ARCA</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($d['status'] === 'pending_admin'): ?>
                                    <form action="index.php?page=admin_ddjj" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="resolve_ddjj">
                                        <input type="hidden" name="ddjj_id" value="<?= $d['id'] ?>">
                                        <button type="submit" name="status" value="approved" class="btn btn-sm btn-success" onclick="return confirm('¿Aprobar y habilitar presentación a ARCA?');">
                                            <i class="bi bi-check-lg"></i> Aprobar
                                        </button>
                                        <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-lg"></i> Rechazar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Revisada</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ddjjs)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No hay Declaraciones Juradas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

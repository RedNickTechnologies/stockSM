<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Declaraciones Juradas (Libro IVA / Balance)</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateDDJJModal">
            <i class="bi bi-file-earmark-plus"></i> Generar DDJJ Mensual
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Período (Mes/Año)</th>
                            <th>Total Facturado</th>
                            <th>Impuestos (IVA Estimado)</th>
                            <th>Estado</th>
                            <th>Fecha Generación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ddjjs as $d): ?>
                        <tr>
                            <td class="fw-bold"><?= str_pad($d['month'], 2, '0', STR_PAD_LEFT) ?> / <?= $d['year'] ?></td>
                            <td>$<?= number_format($d['total_sales'], 2) ?></td>
                            <td>$<?= number_format($d['total_taxes'], 2) ?></td>
                            <td>
                                <?php if($d['status'] === 'pending_admin'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pendiente Aprobación Admin</span>
                                <?php elseif($d['status'] === 'approved'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aprobado (Listo para ARCA)</span>
                                <?php elseif($d['status'] === 'sent_to_arca'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-cloud-check"></i> Presentado en ARCA</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazado por Admin</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                            <td>
                                <?php if($d['status'] === 'approved'): ?>
                                    <button class="btn btn-sm btn-info text-dark fw-bold" onclick="alert('Funcionalidad de Envío a ARCA en desarrollo (Mock). En la versión final esto enviaría el lote completo a los servidores de AFIP/ARCA.');">
                                        <i class="bi bi-cloud-arrow-up"></i> Presentar a ARCA
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ddjjs)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay DDJJs generadas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate DDJJ -->
<div class="modal fade" id="generateDDJJModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=accountant_ddjj" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Generar DDJJ / Balance Mensual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="generate">
                    
                    <p class="small text-muted">El sistema calculará la suma de todas las facturas <strong>aprobadas</strong> para el período seleccionado y estimará la liquidación de IVA.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="month" class="form-select" required>
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>" <?= date('n') == $i ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Calcular y Enviar a Aprobación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h2 class="mb-4">Facturación y AFIP / ARCA</h2>

    <div class="card shadow-sm border-primary">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Factura</th>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                            <th>Estado Interno</th>
                            <th>Estado ARCA</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $s): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($s['id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($s['username']) ?></td>
                            <td><?= $s['created_at'] ?></td>
                            <td class="fw-bold">$<?= number_format($s['total'], 2) ?></td>
                            <td>
                                <?php if($s['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php elseif($s['status'] === 'approved'): ?>
                                    <span class="badge bg-success">Aprobada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($s['status'] === 'approved'): ?>
                                    <?php if($s['arca_id']): ?>
                                        <span class="badge bg-info text-dark"><i class="bi bi-cloud-check"></i> Emitida (CAE: <?= $s['cae'] ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Emitida</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=view_invoice&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" target="_blank" title="Ver Factura Interna">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                
                                <?php if($s['status'] === 'pending'): ?>
                                    <form action="index.php?page=accountant_sales" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Aprobar Internamente">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="index.php?page=accountant_sales" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rechazar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                <?php elseif($s['status'] === 'approved' && !$s['arca_id']): ?>
                                    <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#emitArcaModal<?= $s['id'] ?>">
                                        <i class="bi bi-send-check"></i> Emitir a ARCA
                                    </button>

                                    <!-- Modal ARCA Emission -->
                                    <div class="modal fade text-start" id="emitArcaModal<?= $s['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="index.php?page=accountant_sales" method="POST" class="arca-form">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Emisión de Comprobante Electrónico</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="emit_arca">
                                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                                        
                                                        <div class="alert alert-info">
                                                            <i class="bi bi-info-circle"></i> Vas a informar la Factura N° <?= $s['id'] ?> por un total de <strong>$<?= number_format($s['total'], 2) ?></strong> a la base de datos de ARCA.
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">CUIT del Cliente</label>
                                                            <input type="text" name="cuit_cliente" class="form-control" placeholder="Ej: 20-12345678-9" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Tipo de Comprobante</label>
                                                            <select name="tipo_factura" class="form-select" required>
                                                                <option value="Factura A">Factura A (Responsable Inscripto)</option>
                                                                <option value="Factura B">Factura B (Consumidor Final)</option>
                                                                <option value="Factura C">Factura C (Monotributo)</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="loading-overlay d-none text-center p-3">
                                                            <div class="spinner-border text-primary" role="status"></div>
                                                            <p class="mt-2 fw-bold text-primary">Conectando con WebServices ARCA...</p>
                                                            <small class="text-muted">Solicitando CAE. Por favor no cierre esta ventana.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary btn-emit">Confirmar Emisión</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled><i class="bi bi-check-all"></i> Listo</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sales)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No hay facturas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.arca-form').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('.btn-emit');
        const overlay = this.querySelector('.loading-overlay');
        btn.disabled = true;
        btn.innerText = "Procesando...";
        overlay.classList.remove('d-none');
    });
});
</script>

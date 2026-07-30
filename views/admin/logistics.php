<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Panel de Logística Integral</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransferModal">
            <i class="bi bi-truck"></i> Solicitar Nuevo Envío a Sucursal
        </button>
    </div>

    <div class="row">
        <!-- Ingresos de Stock (Renovaciones) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Solicitudes de Ingreso (Transportistas)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Transp.</th>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($renewals as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['transporter_name']) ?></td>
                                    <td><?= htmlspecialchars($r['name']) ?></td>
                                    <td class="fw-bold">+<?= $r['quantity'] ?></td>
                                    <td>
                                        <?php if($r['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php elseif($r['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Aprobado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rechazado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($r['status'] === 'pending'): ?>
                                        <form action="index.php?page=admin_logistics" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="resolve_renewal">
                                            <input type="hidden" name="renewal_id" value="<?= $r['id'] ?>">
                                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Envíos (Transfers) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Envíos a Sucursales (Despachos)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>N° Viaje</th>
                                    <th>Sucursal</th>
                                    <th>Chofer</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transfers as $t): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $t['id'] ?></td>
                                    <td><?= htmlspecialchars($t['branch_address']) ?></td>
                                    <td><?= $t['transporter_name'] ? htmlspecialchars($t['transporter_name']) : '<em class="text-muted">Ninguno</em>' ?></td>
                                    <td>
                                        <?php if($t['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php elseif($t['status'] === 'accepted'): ?>
                                            <span class="badge bg-info text-dark">En Tránsito</span>
                                        <?php elseif($t['status'] === 'delivered'): ?>
                                            <span class="badge bg-success">Entregado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Fallido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Transfer -->
<div class="modal fade" id="createTransferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?page=admin_logistics" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Crear Envío a Sucursal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create_transfer">
                    
                    <div class="alert alert-info small">
                        <strong>Nota:</strong> Al crear este envío, el stock se descontará inmediatamente de tu tienda principal (quedará "En Tránsito"). Si el transportista no logra entregar el pedido, el stock retornará.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección de Sucursal Destino</label>
                        <input type="text" name="branch_address" class="form-control" required placeholder="Ej: Sucursal Norte - Av. Siempre Viva 123">
                    </div>

                    <h6 class="mt-4 border-bottom pb-2">Productos a Enviar</h6>
                    <div id="transferProductsContainer">
                        <div class="row mb-2 transfer-row">
                            <div class="col-md-8">
                                <select name="product_id[]" class="form-select" required>
                                    <option value="">Seleccionar Producto...</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (Disp: <?= $p['stock'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="quantity[]" class="form-control" placeholder="Cantidad" min="1" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addTransferRow()">+ Añadir otro producto</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Envío y Descontar Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addTransferRow() {
    const container = document.getElementById('transferProductsContainer');
    const template = container.querySelector('.transfer-row').cloneNode(true);
    template.querySelectorAll('input').forEach(input => input.value = '');
    template.querySelectorAll('select').forEach(select => select.value = '');
    container.appendChild(template);
}
</script>

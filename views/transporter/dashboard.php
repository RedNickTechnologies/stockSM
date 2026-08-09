<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Panel de Transportista</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestStockModal">
            <i class="bi bi-box-arrow-in-down"></i> Registrar Ingreso de Mercadería
        </button>
    </div>

    <!-- Estado del Vehículo -->
    <?php if(!$active_vehicle_request): ?>
    <div class="alert alert-warning shadow-sm border-warning">
        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Vehículo no asignado</h4>
        <p>Debes solicitar un vehículo (propio o de la empresa) y esperar la aprobación del administrador antes de poder aceptar viajes.</p>
        <hr>
        <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#requestVehicleModal">
            <i class="bi bi-car-front"></i> Solicitar Vehículo para Jornada
        </button>
    </div>
    <?php else: ?>
    <div class="card shadow-sm mb-4 <?= $active_vehicle_request['status'] === 'approved' ? 'border-success' : 'border-secondary' ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1"><i class="bi bi-truck"></i> Vehículo de Trabajo Actual</h5>
                    <p class="text-muted mb-0">
                        <?php if($active_vehicle_request['is_own_vehicle']): ?>
                            Propio: <?= htmlspecialchars($active_vehicle_request['own_type'] . ' - ' . $active_vehicle_request['own_license_plate']) ?> (<?= $active_vehicle_request['own_weight_capacity'] ?>kg)
                        <?php else: ?>
                            Empresa: <?= htmlspecialchars($active_vehicle_request['brand'] . ' ' . $active_vehicle_request['model'] . ' - Patente: ' . $active_vehicle_request['license_plate']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-end">
                    <?php if($active_vehicle_request['status'] === 'pending'): ?>
                        <span class="badge bg-secondary p-2 fs-6 mb-2"><i class="bi bi-clock"></i> Esperando Aprobación de Admin...</span>
                    <?php else: ?>
                        <span class="badge bg-success p-2 fs-6 mb-2"><i class="bi bi-check-circle"></i> Aprobado (Listo para operar)</span>
                        <br>
                        <form action="index.php?page=transporter_dashboard" method="POST" class="d-inline" onsubmit="return confirm('¿Finalizar jornada y devolver vehículo?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="end_shift">
                            <input type="hidden" name="request_id" value="<?= $active_vehicle_request['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Finalizar Jornada</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-truck"></i> Envíos y Viajes (Sucursales)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Viaje</th>
                            <th>Sucursal Destino</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfers as $t): ?>
                        <tr>
                            <td class="fw-bold">#<?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['branch_address']) ?></td>
                            <td>
                                <?php if($t['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente de Chofer</span>
                                <?php elseif($t['status'] === 'accepted'): ?>
                                    <span class="badge bg-info text-dark">En Tránsito (Tu Viaje)</span>
                                <?php elseif($t['status'] === 'delivered'): ?>
                                    <span class="badge bg-success">Entregado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Entrega Fallida</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $t['created_at'] ?></td>
                            <td class="text-end">
                                <?php if($t['status'] !== 'pending'): ?>
                                    <a href="index.php?page=view_transfer_pdf&id=<?= $t['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                <?php endif; ?>

                                <?php if($t['status'] === 'pending'): ?>
                                    <?php if($active_vehicle_request && $active_vehicle_request['status'] === 'approved'): ?>
                                        <form action="index.php?page=transporter_dashboard" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="accept_transfer">
                                            <input type="hidden" name="transfer_id" value="<?= $t['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Aceptar Viaje</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled title="Necesitas un vehículo aprobado">Aceptar Viaje</button>
                                    <?php endif; ?>
                                <?php elseif($t['status'] === 'accepted' && $t['transporter_id'] === $_SESSION['user_id']): ?>
                                    <form action="index.php?page=transporter_dashboard" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="finish_transfer">
                                        <input type="hidden" name="transfer_id" value="<?= $t['id'] ?>">
                                        <input type="hidden" name="outcome" value="delivered">
                                        <button type="submit" class="btn btn-sm btn-success mb-1" title="Entregado"><i class="bi bi-check-circle"></i> Entregado</button>
                                    </form>
                                    <form action="index.php?page=transporter_dashboard" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="finish_transfer">
                                        <input type="hidden" name="transfer_id" value="<?= $t['id'] ?>">
                                        <input type="hidden" name="outcome" value="failed">
                                        <button type="submit" class="btn btn-sm btn-danger mb-1" title="Falló"><i class="bi bi-x-circle"></i> Falló</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($transfers)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No hay viajes disponibles ni en curso.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cargas Asignadas (Ventas) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Cargas Asignadas (Ventas a Clientes)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Venta</th>
                            <th>Vendedor</th>
                            <th>Monto</th>
                            <th>Fecha Asignación</th>
                            <th>Estado Actual</th>
                            <th class="text-end">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assigned_sales as $s): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($s['id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($s['seller_name']) ?></td>
                            <td>$<?= number_format($s['total'], 2) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                            <td>
                                <?php if($s['transport_status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente de Acción</span>
                                <?php elseif($s['transport_status'] === 'accepted'): ?>
                                    <span class="badge bg-info text-dark">Carga Aceptada</span>
                                <?php elseif($s['transport_status'] === 'in_transit'): ?>
                                    <span class="badge bg-primary">En Tránsito</span>
                                <?php elseif($s['transport_status'] === 'delivered'): ?>
                                    <span class="badge bg-success">Entregada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=view_invoice&id=<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> Ver Factura</a>
                                
                                <?php if(in_array($s['transport_status'], ['pending', 'accepted', 'in_transit'])): ?>
                                    <form action="index.php?page=transporter_dashboard" method="POST" class="d-inline ms-1">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="update_sale_status">
                                        <input type="hidden" name="sale_id" value="<?= $s['id'] ?>">
                                        <select name="transport_status" class="form-select form-select-sm d-inline-block w-auto" required onchange="if(confirm('¿Confirmar cambio de estado?')) this.form.submit(); else this.selectedIndex = 0;">
                                            <option value="">Actualizar Estado...</option>
                                            <option value="accepted">Aceptar Carga</option>
                                            <option value="in_transit">En Tránsito</option>
                                            <option value="delivered">Entregada (Finalizar)</option>
                                            <option value="rejected">Rechazar Carga</option>
                                        </select>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($assigned_sales)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No tienes cargas de ventas asignadas por el momento.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tus Solicitudes de Ingreso de Mercadería</h5>
            <?php if($active_vehicle_request && $active_vehicle_request['status'] === 'approved'): ?>
            <a href="index.php?page=view_transporter_stock" target="_blank" class="btn btn-sm btn-outline-light">
                <i class="bi bi-file-earmark-pdf"></i> Imprimir Hoja de Ruta
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Req</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($renewals as $r): ?>
                        <tr>
                            <td>#<?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td class="fw-bold">+<?= $r['quantity'] ?></td>
                            <td>
                                <?php if($r['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente de Aprobación (Admin)</span>
                                <?php elseif($r['status'] === 'approved'): ?>
                                    <span class="badge bg-success">Aprobado e Ingresado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $r['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($renewals)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No has realizado ingresos de stock.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Request Stock -->
<div class="modal fade" id="requestStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=transporter_dashboard" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Ingreso de Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="request_stock">
                    
                    <div class="mb-3">
                        <label class="form-label">Producto a ingresar</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad Traída</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar a Revisión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Request Vehicle -->
<div class="modal fade" id="requestVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?page=transporter_dashboard" method="POST">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Solicitar Vehículo para la Jornada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="request_vehicle">
                    
                    <div class="mb-3">
                        <label class="form-label">¿Qué vehículo utilizarás?</label>
                        <select name="vehicle_choice" class="form-select" id="vehicleChoiceSelect" onchange="toggleVehicleForms()" required>
                            <option value="">Seleccione...</option>
                            <option value="company">Vehículo de la Empresa</option>
                            <option value="own">Mi Vehículo Propio</option>
                        </select>
                    </div>

                    <div id="companyVehicleForm" style="display:none;" class="p-3 border rounded bg-light mb-3">
                        <h6>Seleccionar Vehículo de la Flota</h6>
                        <select name="vehicle_id" class="form-select" id="companyVehicleSelect">
                            <option value="">Seleccione un vehículo disponible...</option>
                            <?php foreach ($available_vehicles as $v): ?>
                                <option value="<?= $v['id'] ?>">
                                    <?= htmlspecialchars($v['type'] . ' - ' . $v['brand'] . ' ' . $v['model'] . ' (Patente: ' . $v['license_plate'] . ') - Soporta: ' . $v['weight_capacity'] . 'kg') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if(empty($available_vehicles)): ?>
                            <small class="text-danger">No hay vehículos de la empresa disponibles en este momento.</small>
                        <?php endif; ?>
                    </div>

                    <div id="ownVehicleForm" style="display:none;" class="p-3 border rounded bg-light mb-3">
                        <h6>Datos de tu Vehículo Propio</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Patente</label>
                                <input type="text" name="own_license_plate" class="form-control" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Tipo de Vehículo</label>
                                <select name="own_type" class="form-select">
                                    <option value="Auto">Automóvil</option>
                                    <option value="Camioneta">Camioneta</option>
                                    <option value="Camion">Camión</option>
                                    <option value="Moto">Motocicleta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Peso Soportado (kg)</label>
                                <input type="number" step="0.01" name="own_weight_capacity" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Espacio Disponible (m³)</label>
                                <input type="number" step="0.01" name="own_volume_capacity" class="form-control">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label text-danger fw-bold">Gasto Estimado de Viaje (a cubrir por la empresa)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="estimated_cost" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Solicitud a Revisión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleVehicleForms() {
    const choice = document.getElementById('vehicleChoiceSelect').value;
    const compForm = document.getElementById('companyVehicleForm');
    const ownForm = document.getElementById('ownVehicleForm');
    
    compForm.style.display = 'none';
    ownForm.style.display = 'none';
    
    document.getElementById('companyVehicleSelect').required = false;
    document.querySelector('input[name="own_license_plate"]').required = false;
    document.querySelector('input[name="estimated_cost"]').required = false;

    if (choice === 'company') {
        compForm.style.display = 'block';
        document.getElementById('companyVehicleSelect').required = true;
    } else if (choice === 'own') {
        ownForm.style.display = 'block';
        document.querySelector('input[name="own_license_plate"]').required = true;
        document.querySelector('input[name="estimated_cost"]').required = true;
    }
}
</script>

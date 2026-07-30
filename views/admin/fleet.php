<div class="container mt-4">
    <h2>Gestión de Flota y Logística</h2>

    <!-- Resumen de Gastos -->
    <div class="row mt-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cash-coin"></i> Total Gastos a Pagar (Fletes)</h5>
                    <h2 class="display-5">$<?= number_format($total_expenses, 2) ?></h2>
                    <small>Por uso de vehículos propios de transportistas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de Vehículos de la Empresa -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Flota de la Empresa</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createVehicleModal">
                <i class="bi bi-plus-lg"></i> Añadir Vehículo
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Patente</th>
                            <th>Vehículo</th>
                            <th>Tipo</th>
                            <th>Capacidad (Peso/Vol)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($v['license_plate']) ?></td>
                            <td><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></td>
                            <td>
                                <?php if($v['type'] == 'car') echo '<i class="bi bi-car-front"></i> Auto'; ?>
                                <?php if($v['type'] == 'pickup') echo '<i class="bi bi-truck-flatbed"></i> Camioneta'; ?>
                                <?php if($v['type'] == 'truck') echo '<i class="bi bi-truck"></i> Camión'; ?>
                            </td>
                            <td><?= number_format($v['weight_capacity'], 2) ?> kg / <?= number_format($v['volume_capacity'], 2) ?> m³</td>
                            <td>
                                <?php if($v['status'] == 'available'): ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php elseif($v['status'] == 'requested'): ?>
                                    <span class="badge bg-warning text-dark">Solicitado</span>
                                <?php elseif($v['status'] == 'in_use'): ?>
                                    <span class="badge bg-info text-dark">En Uso</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Mantenimiento</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(in_array($v['status'], ['available', 'maintenance'])): ?>
                                <form action="index.php?page=admin_fleet" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="toggle_vehicle">
                                    <input type="hidden" name="vehicle_id" value="<?= $v['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $v['status'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $v['status'] == 'available' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <?= $v['status'] == 'available' ? 'A Mantenimiento' : 'Habilitar' ?>
                                    </button>
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

    <!-- Solicitudes de Viaje / Vehículo -->
    <div class="card shadow-sm border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Solicitudes de Viaje (Transportistas)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transportista</th>
                            <th>Vehículo a Usar</th>
                            <th>Gasto a Cubrir</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td>#<?= $r['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($r['transporter_name']) ?></td>
                            <td>
                                <?php if($r['is_own_vehicle']): ?>
                                    <span class="badge bg-secondary"><i class="bi bi-person-fill"></i> Propio</span><br>
                                    <small><?= htmlspecialchars($r['own_type'] . ' | ' . $r['own_license_plate']) ?></small><br>
                                    <small class="text-muted"><?= $r['own_weight_capacity'] ?>kg / <?= $r['own_volume_capacity'] ?>m³</small>
                                <?php else: ?>
                                    <span class="badge bg-primary"><i class="bi bi-building"></i> Empresa</span><br>
                                    <small class="fw-bold"><?= htmlspecialchars($r['comp_type'] . ' | ' . $r['comp_plate']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-danger fw-bold">
                                <?= $r['is_own_vehicle'] && $r['estimated_cost'] > 0 ? '$'.number_format($r['estimated_cost'], 2) : '-' ?>
                            </td>
                            <td>
                                <?php if($r['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php elseif($r['status'] === 'approved'): ?>
                                    <span class="badge bg-success">Aprobado / Viajando</span>
                                <?php elseif($r['status'] === 'completed'): ?>
                                    <span class="badge bg-info text-dark">Finalizado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($r['status'] === 'pending'): ?>
                                <form action="index.php?page=admin_fleet" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="resolve_request">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <button type="submit" name="status" value="approved" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($requests)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay solicitudes de viaje.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Vehicle -->
<div class="modal fade" id="createVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_fleet" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Añadir Vehículo a la Flota</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create_vehicle">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Patente</label>
                            <input type="text" name="license_plate" class="form-control" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="car">Automóvil</option>
                                <option value="pickup">Camioneta</option>
                                <option value="truck">Camión</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="brand" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="model" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacidad de Carga (kg)</label>
                            <input type="number" step="0.01" name="weight_capacity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Volumen (m³)</label>
                            <input type="number" step="0.01" name="volume_capacity" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

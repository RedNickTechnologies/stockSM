<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Usuarios</h2>
        <div>
            <a href="index.php?page=export_users_pdf" target="_blank" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre / Email</th>
                        <th>Rol</th>
                        <th>Meta Mensual</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($u['username']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($u['email'] ?? 'Sin email') ?></div>
                        </td>
                        <td>
                            <?php if($u['role'] === 'admin'): ?>
                                <span class="badge bg-primary">Administrador</span>
                            <?php elseif($u['role'] === 'accountant'): ?>
                                <span class="badge bg-success text-white"><i class="bi bi-calculator"></i> Contador</span>
                            <?php elseif($u['role'] === 'transporter'): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-truck"></i> Transportista</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">Vendedor</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            $<?= number_format($u['monthly_goal'], 2) ?>
                        </td>
                        <td><?= $u['created_at'] ?></td>
                        <td>
                            <?= $u['is_active'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inhabilitado</span>' ?>
                        </td>
                        <td class="text-end">
                            <?php if($u['id'] !== $_SESSION['user_id']): ?>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editGoalModal<?= $u['id'] ?>">
                                    <i class="bi bi-pencil"></i> Meta
                                </button>

                                <form action="index.php?page=admin_users" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $u['is_active'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <i class="bi <?= $u['is_active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                        <?= $u['is_active'] ? 'Inhabilitar' : 'Habilitar' ?>
                                    </button>
                                </form>
                                
                                <!-- Modal Edit Goal -->
                                <div class="modal fade" id="editGoalModal<?= $u['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="index.php?page=admin_users" method="POST">
                                                <div class="modal-header bg-primary text-white text-start">
                                                    <h5 class="modal-title">Editar Meta para <?= htmlspecialchars($u['username']) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="edit_goal">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Meta Mensual ($)</label>
                                                        <input type="number" step="0.01" name="monthly_goal" class="form-control" value="<?= $u['monthly_goal'] ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Meta</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Tú (No editable)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create User -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_users" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" name="username" class="form-control" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (Requerido para Tickets)</label>
                        <input type="email" name="email" class="form-control" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña Temporal</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol del Sistema</label>
                        <select name="role" class="form-select" required>
                            <option value="user">Vendedor (Usuario Estándar)</option>
                            <option value="transporter">Transportista (Logística)</option>
                            <option value="accountant">Contador (Balances y Facturación)</option>
                            <option value="admin">Administrador (Control Total)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Mensual de Ventas ($) (Opcional)</label>
                        <input type="number" step="0.01" name="monthly_goal" class="form-control" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

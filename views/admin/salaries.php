<div class="container mt-4">
    <h2><i class="bi bi-cash-coin"></i> Gestión de RRHH - Liquidaciones de Sueldo</h2>

    <div class="row mt-4">
        <!-- Columna Formulario Generación -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Generar Nuevo Recibo</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?page=admin_salaries" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="generate_salary">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Empleado</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Seleccionar empleado...</option>
                                <?php foreach($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['username']) ?> (<?= ucfirst($emp['role']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Mes</label>
                                <select name="period_month" class="form-select" required>
                                    <?php for($m=1; $m<=12; $m++): ?>
                                        <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Año</label>
                                <input type="number" name="period_year" class="form-control" value="<?= date('Y') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Sueldo Básico ($)</label>
                            <input type="number" step="0.01" name="base_salary" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deducciones ($)</label>
                            <input type="number" step="0.01" name="deductions" class="form-control" value="0.00" required>
                            <small class="text-muted">Obra social, jubilación, sindicato, etc.</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Confirmar generación del recibo? El empleado podrá visualizarlo inmediatamente.');">
                            Generar y Publicar Recibo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Historial -->
        <div class="col-md-8">
            <div class="card shadow-sm border-secondary">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Historial de Recibos Emitidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>N° Ref</th>
                                    <th>Empleado</th>
                                    <th>Período</th>
                                    <th>Sueldo Neto</th>
                                    <th>Fecha Emisión</th>
                                    <th class="text-end">Recibo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($salaries as $s): ?>
                                <tr>
                                    <td class="fw-bold">#<?= str_pad($s['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($s['username']) ?> <span class="badge bg-secondary"><?= ucfirst($s['role']) ?></span></td>
                                    <td><?= str_pad($s['period_month'], 2, '0', STR_PAD_LEFT) . '/' . $s['period_year'] ?></td>
                                    <td class="fw-bold text-success">$<?= number_format($s['net_salary'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="index.php?page=view_salary_pdf&id=<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($salaries)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">Aún no se han emitido recibos de sueldo.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Reportes de Estado General de Empresa</h2>
        <form action="index.php?page=admin_reports" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="generate">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-file-earmark-plus"></i> Generar Reporte de este Mes
            </button>
        </form>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Los reportes mensuales realizan una captura del estado de ventas, altas de usuarios y gastos logísticos para el mes actual. Si ya se ha generado un reporte para este mes, no se duplicará.
    </div>

    <!-- Configuración Automática -->
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body bg-light">
            <form action="index.php?page=admin_reports" method="POST" class="row align-items-center">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="action" value="save_settings">
                <div class="col-auto">
                    <label for="auto_report_day" class="col-form-label fw-bold"><i class="bi bi-robot"></i> Generación Automática:</label>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text">Día del mes</span>
                        <select name="auto_report_day" id="auto_report_day" class="form-select" style="max-width: 100px;">
                            <?php for($i=1; $i<=28; $i++): ?>
                                <option value="<?= $i ?>" <?= $auto_report_day == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">Guardar Configuración</button>
                </div>
                <div class="col-12 mt-2 text-muted small">
                    El sistema generará el reporte de forma automática a primera hora de este día cada mes.
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Mes/Año</th>
                            <th>Monto Facturado</th>
                            <th>Cant. Ventas</th>
                            <th>Nuevos Usuarios</th>
                            <th>Gastos Flota Propia</th>
                            <th>Fecha de Generación</th>
                            <th class="text-end">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                        <tr>
                            <td class="fw-bold"><?= str_pad($r['month'], 2, '0', STR_PAD_LEFT) ?> / <?= $r['year'] ?></td>
                            <td class="text-success fw-bold">$<?= number_format($r['total_sales_amount'], 2) ?></td>
                            <td><?= $r['total_sales_count'] ?></td>
                            <td><?= $r['new_users_count'] ?></td>
                            <td class="text-danger fw-bold">$<?= number_format($r['total_expenses'], 2) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick="printReport(this.closest('tr'))">
                                    <i class="bi bi-printer"></i> Imprimir PDF
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($reports)): ?>
                            <tr><td colspan="7" class="text-center text-muted">Aún no se ha generado ningún reporte mensual.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function printReport(row) {
    const monthYear = row.cells[0].innerText;
    const salesAmount = row.cells[1].innerText;
    const salesCount = row.cells[2].innerText;
    const newUsers = row.cells[3].innerText;
    const expenses = row.cells[4].innerText;
    const dateGen = row.cells[5].innerText;

    const printWin = window.open('', '_blank');
    printWin.document.write(`
        <html><head><title>Reporte Mensual ${monthYear}</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            h1 { border-bottom: 2px solid #000; padding-bottom: 10px; text-align: center; }
            .metrics { margin-top: 30px; font-size: 18px; line-height: 1.8; }
            .metric { display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; padding: 10px 0; }
            .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
        </style>
        </head><body>
            <h1>Reporte de Estado General - ${monthYear}</h1>
            <p><strong>Fecha de Generación:</strong> ${dateGen}</p>
            <div class="metrics">
                <div class="metric"><span>Monto Total Facturado:</span> <strong>${salesAmount}</strong></div>
                <div class="metric"><span>Cantidad de Ventas Realizadas:</span> <strong>${salesCount}</strong></div>
                <div class="metric"><span>Nuevos Usuarios Registrados:</span> <strong>${newUsers}</strong></div>
                <div class="metric"><span>Gastos de Flota (Vehículos Propios):</span> <strong style="color:red">${expenses}</strong></div>
            </div>
            <div class="footer">
                <p>Reporte Oficial de SuperMarket Stock</p>
            </div>
        </body></html>
    `);
    printWin.document.close();
    printWin.focus();
    printWin.print();
}
</script>

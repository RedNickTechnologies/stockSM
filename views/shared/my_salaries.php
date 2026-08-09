<div class="container mt-4">
    <h2><i class="bi bi-wallet2"></i> Mis Liquidaciones de Sueldo</h2>
    <p class="text-muted">Aquí podrás visualizar y descargar tus recibos de sueldo oficiales emitidos por Recursos Humanos.</p>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Ref</th>
                            <th>Período</th>
                            <th>Sueldo Básico</th>
                            <th>Deducciones</th>
                            <th>Sueldo Neto a Cobrar</th>
                            <th>Fecha Emisión</th>
                            <th class="text-end">Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salaries as $s): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($s['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="fw-bold"><?= str_pad($s['period_month'], 2, '0', STR_PAD_LEFT) . '/' . $s['period_year'] ?></td>
                            <td>$<?= number_format($s['base_salary'], 2) ?></td>
                            <td class="text-danger">-$<?= number_format($s['deductions'], 2) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($s['net_salary'], 2) ?></td>
                            <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="index.php?page=view_salary_pdf&id=<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF Oficial
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($salaries)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No se han emitido recibos de sueldo para tu cuenta todavía.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

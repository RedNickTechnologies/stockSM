<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-shield-lock-fill text-danger"></i> Caja de Seguridad (Log de Auditoría)</h2>
        <a href="index.php?page=export_audit_pdf" target="_blank" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf"></i> Exportar Logs a PDF
        </a>
    </div>
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Estos registros son inmutables. Ningún usuario ni administrador puede borrarlos o alterarlos desde la aplicación, garantizando un historial transparente de todas las acciones importantes.
    </div>

    <div class="card shadow-sm border-danger">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario Autor</th>
                            <th>Tipo de Acción</th>
                            <th>Detalles del Evento</th>
                            <th>Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-nowrap"><?= $log['created_at'] ?></td>
                            <td class="fw-bold"><?= $log['username'] ? htmlspecialchars($log['username']) : 'Sistema' ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($log['action']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($log['details']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($log['ip_address']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

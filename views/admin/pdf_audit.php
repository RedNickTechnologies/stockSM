<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Auditoría - SuperMarket</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .table th { background-color: #f4f4f4; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
        @media print {
            body { font-size: 11pt; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>SuperMarket - Caja de Seguridad (Log de Auditoría)</h1>
        <p>Fecha de emisión: <?= date('d/m/Y H:i') ?></p>
        <p>Mostrando los últimos registros del sistema.</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Detalles</th>
                <th>Fecha/Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $log['id'] ?></td>
                <td><?= htmlspecialchars($log['username'] ?? 'Sistema') ?></td>
                <td><strong><?= htmlspecialchars($log['action']) ?></strong></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Documento de control interno. Generado automáticamente por el sistema SuperMarket Stock.</p>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios - SuperMarket</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
        @media print {
            body { font-size: 12pt; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>SuperMarket - Reporte de Usuarios</h1>
        <p>Fecha de emisión: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Meta Mensual</th>
                <th>Estado</th>
                <th>Fecha Ingreso</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email'] ?? 'N/A') ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td>$<?= number_format($u['monthly_goal'], 2) ?></td>
                <td><?= $u['is_active'] ? 'Activo' : 'Inhabilitado' ?></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema SuperMarket Stock.</p>
    </div>

</body>
</html>

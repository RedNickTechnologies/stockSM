<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Vehículos - SuperMarket</title>
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
        <h1>SuperMarket - Reporte de Flota de Vehículos</h1>
        <p>Fecha de emisión: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Patente</th>
                <th>Marca / Modelo</th>
                <th>Tipo</th>
                <th>Capacidad (Peso)</th>
                <th>Capacidad (Volumen)</th>
                <th>Estado Actual</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehicles as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['license_plate']) ?></td>
                <td><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></td>
                <td><?= ucfirst($v['type']) ?></td>
                <td><?= number_format($v['weight_capacity'], 2) ?> kg</td>
                <td><?= number_format($v['volume_capacity'], 2) ?> m³</td>
                <td><?= ucfirst(str_replace('_', ' ', $v['status'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema SuperMarket Stock.</p>
    </div>

</body>
</html>

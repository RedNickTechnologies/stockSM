<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Ruta - Vehículo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; background: #f4f6f9; }
        .doc-container { background: #fff; max-width: 800px; margin: 2rem auto; padding: 3rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        @media print { .no-print { display: none !important; } body { background: #fff; } .doc-container { margin: 0; padding: 0; box-shadow: none; } }
    </style>
</head>
<body>

<div class="text-center mt-4 mb-2 no-print">
    <button class="btn btn-danger btn-lg me-2 fw-bold" onclick="generatePDF()">Descargar PDF</button>
    <button class="btn btn-secondary btn-lg fw-bold" onclick="window.print()">Imprimir</button>
</div>

<div class="doc-container" id="pdfArea">
    <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
        <div>
            <h2 class="fw-bold">SUPERMARKET</h2>
            <div class="text-muted fs-5">Logística - Control de Tránsito</div>
        </div>
        <div class="text-end">
            <h3 class="text-secondary fw-bold">HOJA DE RUTA / STOCK</h3>
            <h4 class="mb-0 text-dark"><?= date('d/m/Y') ?></h4>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-sm-6">
            <h6 class="text-muted mb-1">Datos del Transportista:</h6>
            <div class="fw-bold fs-5 text-dark"><?= htmlspecialchars($_SESSION['username']) ?></div>
            <div class="text-muted">Autorizado y Activo</div>
        </div>
        <div class="col-sm-6 text-sm-end">
            <h6 class="text-muted mb-1">Vehículo Asignado:</h6>
            <?php if($vehicle['is_own_vehicle']): ?>
                <div class="fw-bold fs-5 text-dark">Vehículo Propio (<?= htmlspecialchars($vehicle['own_type']) ?>)</div>
                <div class="mb-1"><strong>Patente:</strong> <?= htmlspecialchars($vehicle['own_license_plate']) ?></div>
                <div class="mb-1"><strong>Capacidad:</strong> <?= $vehicle['own_weight_capacity'] ?>kg / <?= $vehicle['own_volume_capacity'] ?>m³</div>
            <?php else: ?>
                <div class="fw-bold fs-5 text-dark">Flota de la Empresa (<?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']) ?>)</div>
                <div class="mb-1"><strong>Patente:</strong> <?= htmlspecialchars($vehicle['license_plate']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <h5 class="border-bottom pb-2 mb-3">Manifiesto de Carga (Mercadería en Tránsito)</h5>
    
    <table class="table table-bordered border-dark mb-5">
        <thead class="table-light border-dark">
            <tr>
                <th style="width: 15%;">Cód.</th>
                <th style="width: 60%;">Descripción del Producto</th>
                <th class="text-center" style="width: 25%;">Total Carga (Unidades)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock_items as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td class="text-center fw-bold fs-5"><?= $s['total_qty'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($stock_items)): ?>
            <tr>
                <td colspan="3" class="text-center text-muted">No hay carga asignada para este vehículo en este momento.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="alert alert-info border-dark text-dark mt-5 text-center">
        Este documento certifica que la mercadería detallada se encuentra en tránsito desde la Tienda Central hacia la/las sucursales de destino correspondientes.
    </div>
</div>

<script>
function generatePDF() {
    const element = document.getElementById('pdfArea');
    const opt = {
        margin:       0.5,
        filename:     'Hoja_de_Ruta_<?= date('Ymd') ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
</body>
</html>

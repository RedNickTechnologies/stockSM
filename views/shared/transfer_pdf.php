<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Despacho #<?= $transfer['id'] ?></title>
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
            <div class="text-muted fs-5">Despacho y Logística</div>
        </div>
        <div class="text-end">
            <h3 class="text-secondary fw-bold">GUÍA DE DESPACHO / REMITO</h3>
            <h4 class="mb-0 text-dark">N° <?= str_pad($transfer['id'], 6, '0', STR_PAD_LEFT) ?></h4>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-sm-6">
            <h6 class="text-muted mb-1">Destino de la Mercadería:</h6>
            <div class="fw-bold fs-5 text-dark"><?= htmlspecialchars($transfer['branch_address']) ?></div>
        </div>
        <div class="col-sm-6 text-sm-end">
            <div class="mb-1"><strong>Fecha Emisión:</strong> <?= date('d/m/Y H:i', strtotime($transfer['created_at'])) ?></div>
            <div class="mb-1"><strong>Autorizado por:</strong> <?= htmlspecialchars($transfer['admin_name']) ?></div>
            <div class="mb-1"><strong>Transportista:</strong> <?= $transfer['transporter_name'] ? htmlspecialchars($transfer['transporter_name']) : 'Asignación Pendiente' ?></div>
            <div><strong>Estado del Viaje:</strong> 
                <?php if($transfer['status'] === 'delivered') echo '<span class="text-success fw-bold">ENTREGADO</span>';
                      elseif($transfer['status'] === 'failed') echo '<span class="text-danger fw-bold">FALLIDO (Retornado)</span>';
                      else echo '<span class="text-warning fw-bold">EN TRÁNSITO</span>'; ?>
            </div>
        </div>
    </div>

    <table class="table table-bordered border-dark mb-5">
        <thead class="table-light border-dark">
            <tr>
                <th style="width: 15%;">Cod. Producto</th>
                <th style="width: 60%;">Descripción del Producto</th>
                <th class="text-center" style="width: 25%;">Cantidad Enviada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $d): ?>
            <tr>
                <td><?= $d['product_id'] ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td class="text-center fw-bold fs-5"><?= $d['quantity'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mt-5 pt-5 text-center">
        <div class="col-sm-6 mt-4">
            <hr class="w-75 mx-auto border-dark">
            <p class="text-muted mb-0 fw-bold">Firma Despachador (Tienda Origen)</p>
        </div>
        <div class="col-sm-6 mt-4">
            <hr class="w-75 mx-auto border-dark">
            <p class="text-muted mb-0 fw-bold">Firma Receptor (Sucursal Destino)</p>
        </div>
    </div>
</div>

<script>
function generatePDF() {
    const element = document.getElementById('pdfArea');
    const opt = {
        margin:       0.5,
        filename:     'Guia_Despacho_<?= str_pad($transfer['id'], 6, '0', STR_PAD_LEFT) ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
</body>
</html>

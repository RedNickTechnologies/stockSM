<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Arial', sans-serif; background: #f4f6f9; }
        .invoice-container { background: #fff; max-width: 800px; margin: 2rem auto; padding: 3rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; }
        .invoice-header { border-bottom: 2px solid #343a40; padding-bottom: 1.5rem; margin-bottom: 2rem; }
        .invoice-title { font-size: 2.5rem; font-weight: 800; color: #212529; letter-spacing: -1px; }
        .invoice-meta { color: #6c757d; font-size: 0.95rem; }
        .table th { background-color: #f8f9fa; color: #495057; border-top: 1px solid #dee2e6; }
        .total-row { font-size: 1.75rem; font-weight: 800; color: #198754; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-container { box-shadow: none; margin: 0; padding: 0; border: none; }
        }
    </style>
</head>
<body>

<div class="text-center mt-4 mb-2 no-print">
    <button class="btn btn-danger btn-lg me-2 fw-bold" onclick="generatePDF()">
        <i class="bi bi-file-earmark-pdf"></i> Descargar PDF (Alta Calidad)
    </button>
    <button class="btn btn-secondary btn-lg fw-bold" onclick="window.print()">
        <i class="bi bi-printer"></i> Impresión Rápida
    </button>
    <a href="index.php?page=<?= $_SESSION['role'] === 'admin' ? 'admin_sales' : 'user_dashboard' ?>" class="btn btn-outline-dark btn-lg ms-2">Volver</a>
</div>

<div class="invoice-container" id="invoiceArea">
    <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
        <div>
            <h2>SUPERMARKET</h2>
            <div class="text-muted">Sucursal Principal</div>
            <div class="text-muted">CUIT: 30-12345678-9</div>
        </div>
        <div class="text-end">
            <?php if(!empty($sale['tipo_factura'])): ?>
                <h3 class="text-secondary"><?= htmlspecialchars(strtoupper($sale['tipo_factura'])) ?></h3>
            <?php else: ?>
                <h3 class="text-secondary">FACTURA INTERNA</h3>
            <?php endif; ?>
            <h4 class="mb-0">N° <?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <h6 class="text-muted">Cliente:</h6>
            <?php if(!empty($sale['cuit_cliente'])): ?>
                <div class="fw-bold">CUIT: <?= htmlspecialchars($sale['cuit_cliente']) ?></div>
            <?php else: ?>
                <div class="fw-bold">Consumidor Final</div>
            <?php endif; ?>
        </div>
        <div class="col-sm-6 text-end">
            <div class="invoice-meta"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></div>
            <div class="invoice-meta"><strong>Estado:</strong> 
                <?php if($sale['status'] === 'approved') echo '<span class="text-success fw-bold">PAGADA / APROBADA</span>';
                      elseif($sale['status'] === 'pending') echo '<span class="text-warning fw-bold">EN REVISIÓN</span>';
                      else echo '<span class="text-danger fw-bold">RECHAZADA / ANULADA</span>'; ?>
            </div>
            <div class="invoice-meta mt-2"><strong>Atendido por:</strong> <?= htmlspecialchars($sale['username']) ?></div>
        </div>
    </div>

    <table class="table mb-5">
        <thead>
            <tr>
                <th style="width: 10%;">Cant.</th>
                <th style="width: 50%;">Descripción del Producto</th>
                <th class="text-end" style="width: 20%;">Precio Unit.</th>
                <th class="text-end" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $d): ?>
            <tr>
                <td class="fw-bold"><?= $d['quantity'] ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td class="text-end">$<?= number_format($d['unit_price'], 2) ?></td>
                <td class="text-end fw-bold">$<?= number_format($d['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end border-0 fw-bold fs-5 pt-4">TOTAL A PAGAR:</td>
                <td class="text-end border-0 total-row pt-4">$<?= number_format($sale['total'], 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-4">
        <div class="col-8">
            <p class="text-muted small">
                Comprobante generado por sistema. Esta factura 
                <?php if(!empty($sale['cae'])): ?>
                    tiene validez fiscal oficial.
                <?php else: ?>
                    es de uso interno exclusivo. El documento original será entregado en papel según resolución de la empresa.
                <?php endif; ?>
            </p>
            <?php if(!empty($sale['cae'])): ?>
            <div class="border p-2 mt-2 bg-light d-inline-block">
                <div class="fw-bold text-dark" style="font-family: monospace; font-size: 1.5rem; letter-spacing: -1px;">
                    ||| | ||||| | ||||| | || ||||| | |||
                </div>
                <div class="small fw-bold mt-1">CAE: <?= $sale['cae'] ?></div>
                <div class="small">Vto CAE: <?= date('d/m/Y', strtotime($sale['vto_cae'])) ?></div>
                <div class="small fw-bold text-muted mt-1">COMPROBANTE AUTORIZADO (ARCA)</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center text-muted" style="font-size: 0.85rem; margin-top: 5rem;">
        <p class="fw-bold">¡Gracias por su compra en SuperMarket!</p>
        <p>Los cambios y devoluciones se aceptan dentro de los 30 días presentando esta factura.</p>
        <p>Generado de forma automática por el Sistema de ERP.</p>
    </div>
</div>

<script>
function generatePDF() {
    const element = document.getElementById('invoiceArea');
    const opt = {
        margin:       0.5,
        filename:     'Factura_<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 3, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>

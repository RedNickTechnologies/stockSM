<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Sueldo - <?= htmlspecialchars($salary['username']) ?> - <?= $salary['period_month'] . '/' . $salary['period_year'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #e9ecef; color: #000; }
        .receipt-container { background: #fff; max-width: 800px; margin: 2rem auto; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .afip-border { border: 1px solid #000; margin-bottom: 10px; }
        .header-box { display: flex; align-items: center; justify-content: space-between; padding: 15px; }
        .header-box h1 { margin: 0; font-size: 24px; text-transform: uppercase; font-weight: bold; }
        .company-data, .employee-data { font-size: 13px; line-height: 1.5; }
        .employee-box { padding: 15px; display: flex; justify-content: space-between; border-top: none; }
        .items-table { width: 100%; font-size: 13px; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; }
        .items-table th { background-color: #f8f9fa; text-align: center; }
        .items-table td:nth-child(2), .items-table td:nth-child(3) { text-align: right; width: 150px; }
        .totals-box { display: flex; justify-content: space-between; padding: 15px; font-weight: bold; font-size: 15px; border-top: none; }
        .signature-box { height: 100px; border-top: 1px dashed #ccc; margin-top: 50px; text-align: center; padding-top: 10px; color: #666; }
    </style>
</head>
<body onload="generatePDF()">

<div class="container text-center mt-3 d-print-none">
    <button onclick="generatePDF()" class="btn btn-primary"><i class="bi bi-download"></i> Descargar PDF</button>
</div>

<div class="receipt-container" id="receiptArea">
    
    <div class="afip-border header-box">
        <div>
            <h1><?= htmlspecialchars($settings['company_name'] ?? 'Empresa Genérica') ?></h1>
            <div class="company-data mt-2">
                CUIT: <?= htmlspecialchars($settings['company_cuit'] ?? '00-00000000-0') ?><br>
                Domicilio: <?= htmlspecialchars($settings['company_address'] ?? 'Dirección No Configurada') ?><br>
            </div>
        </div>
        <div class="text-end">
            <h2 class="mb-1" style="font-size: 20px;">RECIBO DE HABERES</h2>
            <strong>Ley de Contrato de Trabajo N° 20.744</strong>
        </div>
    </div>

    <div class="afip-border employee-box">
        <div class="employee-data">
            <strong>Apellido y Nombre:</strong> <?= htmlspecialchars($salary['username']) ?><br>
            <strong>Categoría / Puesto:</strong> <?= ucfirst($salary['role']) ?><br>
            <strong>Fecha de Ingreso:</strong> 01/01/2023 (Referencia)
        </div>
        <div class="employee-data text-end">
            <strong>Período Liquidado:</strong> Mes <?= str_pad($salary['period_month'], 2, '0', STR_PAD_LEFT) ?> / Año <?= $salary['period_year'] ?><br>
            <strong>Fecha de Pago:</strong> <?= date('d/m/Y', strtotime($salary['created_at'])) ?><br>
            <strong>CUIL:</strong> 20-00000000-0
        </div>
    </div>

    <table class="items-table afip-border" style="border-top: none; margin-bottom: 0;">
        <thead>
            <tr>
                <th>Conceptos</th>
                <th>Haberes / Remuneraciones</th>
                <th>Retenciones / Deducciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sueldo Básico</td>
                <td>$<?= number_format($salary['base_salary'], 2, ',', '.') ?></td>
                <td></td>
            </tr>
            <tr>
                <td>Descuentos de Ley (Jubilación, Obra Social, Sindicato)</td>
                <td></td>
                <td>$<?= number_format($salary['deductions'], 2, ',', '.') ?></td>
            </tr>
            <!-- Fill empty rows to make the receipt look official -->
            <?php for($i=0; $i<6; $i++): ?>
            <tr><td>&nbsp;</td><td></td><td></td></tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="afip-border totals-box">
        <div>
            TOTALES
        </div>
        <div style="display: flex; gap: 50px;">
            <div>$<?= number_format($salary['base_salary'], 2, ',', '.') ?></div>
            <div>$<?= number_format($salary['deductions'], 2, ',', '.') ?></div>
        </div>
    </div>

    <div class="afip-border p-3 mt-3" style="background-color: #f8f9fa;">
        <h4 class="mb-0 text-end"><strong>SUELDO NETO A COBRAR: $<?= number_format($salary['net_salary'], 2, ',', '.') ?></strong></h4>
        <p class="text-end mb-0 mt-1" style="font-size: 12px; font-style: italic;">
            Son Pesos: <?= class_exists('NumberFormatter') ? ucwords((new NumberFormatter("es", NumberFormatter::SPELLOUT))->format($salary['net_salary'])) : 'Según importe neto expresado en números.' ?>
        </p>
    </div>

    <div class="signature-box">
        Firma del Empleador (Original) / Firma del Empleado (Duplicado)
    </div>

</div>

<script>
function generatePDF() {
    const element = document.getElementById('receiptArea');
    const opt = {
        margin:       0.3,
        filename:     'recibo_sueldo_<?= $salary['period_month'] ?>_<?= $salary['period_year'] ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 3, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>

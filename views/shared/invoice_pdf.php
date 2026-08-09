<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= str_pad($sale['id'], 8, '0', STR_PAD_LEFT) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #e9ecef;
            color: #000;
        }
        .invoice-container {
            background: #fff;
            max-width: 800px;
            margin: 2rem auto;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .afip-border {
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        .header-box {
            display: flex;
            position: relative;
            min-height: 140px;
        }
        .header-left {
            width: 50%;
            padding: 15px;
            border-right: 1px solid #000;
        }
        .header-right {
            width: 50%;
            padding: 15px 15px 15px 50px;
        }
        .header-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 0;
            width: 60px;
            text-align: center;
        }
        .letter-box {
            border: 1px solid #000;
            border-top: none;
            width: 100%;
            height: 50px;
            font-size: 32px;
            font-weight: bold;
            line-height: 50px;
            background: #fff;
        }
        .cod-text {
            font-size: 10px;
            font-weight: bold;
            display: block;
            border: 1px solid #000;
            border-top: none;
            background: #fff;
        }
        .original-text {
            font-size: 10px;
            display: block;
            margin-top: 2px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .company-details {
            font-size: 12px;
            text-align: center;
            line-height: 1.2;
        }
        .invoice-type {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-details {
            font-size: 13px;
            line-height: 1.4;
            font-weight: bold;
        }
        .customer-box {
            display: flex;
            font-size: 12px;
            padding: 10px 15px;
        }
        .customer-left {
            width: 60%;
            line-height: 1.6;
        }
        .customer-right {
            width: 40%;
            line-height: 1.6;
        }
        
        .table-items {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .table-items th {
            background-color: #ddd;
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        .table-items td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            padding: 4px 8px;
        }
        /* Trick to make table take remaining height */
        .items-container {
            min-height: 300px;
            border: 1px solid #000;
            border-top: none;
            margin-bottom: 10px;
        }
        .totals-box {
            display: flex;
            justify-content: flex-end;
            padding: 15px;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.8;
        }
        .totals-grid {
            display: grid;
            grid-template-columns: 150px 100px;
            text-align: right;
        }
        
        .footer-box {
            display: flex;
            align-items: center;
            padding: 10px;
            font-size: 13px;
        }
        .footer-qr {
            width: 80px;
            height: 80px;
            margin-right: 15px;
        }
        .footer-arca-logo {
            font-weight: bold;
            font-size: 24px;
            letter-spacing: 1px;
            line-height: 1;
        }
        .footer-arca-sub {
            font-size: 8px;
            color: #555;
            text-transform: uppercase;
        }
        .footer-cae {
            flex-grow: 1;
            text-align: right;
            font-weight: bold;
            line-height: 1.6;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; margin: 0; padding: 0; }
            .invoice-container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; border: none; }
        }
    </style>
</head>
<body>

<div class="text-center mt-4 mb-3 no-print">
    <button class="btn btn-danger btn-lg me-2 fw-bold" onclick="generatePDF()">
        <i class="bi bi-file-earmark-pdf"></i> Descargar PDF (Alta Calidad)
    </button>
    <button class="btn btn-secondary btn-lg fw-bold" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimir
    </button>
    <?php
        $back_url = 'index.php?page=login';
        if(isset($_SESSION['role'])) {
            if($_SESSION['role'] === 'admin') $back_url = 'index.php?page=admin_sales';
            elseif($_SESSION['role'] === 'user') $back_url = 'index.php?page=user_dashboard';
            elseif($_SESSION['role'] === 'accountant') $back_url = 'index.php?page=accountant_sales';
        }
    ?>
    <a href="<?= $back_url ?>" class="btn btn-outline-dark btn-lg ms-2">Volver</a>
</div>

<div class="invoice-container" id="invoiceArea">
    
    <!-- Top Header -->
    <div class="afip-border header-box">
        <div class="header-left">
            <div class="company-name">SUPERMARKET</div>
            <div class="company-details">
                Nombre y Apellido<br>
                Avenida Falsa 123<br>
                (1000) Capital Federal - Buenos Aires<br>
                (011) 4567-8900<br>
                <strong>Responsable Monotributo</strong>
            </div>
        </div>
        
        <div class="header-center">
            <div class="letter-box"><?= !empty($sale['tipo_factura']) ? substr($sale['tipo_factura'], -1) : 'C' ?></div>
            <div class="cod-text">CÓD. 11</div>
            <div class="original-text">ORIGINAL</div>
        </div>

        <div class="header-right">
            <div class="invoice-type">FACTURA</div>
            <div class="invoice-details">
                N° 0001-<?= str_pad($sale['id'], 8, '0', STR_PAD_LEFT) ?><br>
                Fecha de Emisión: <?= date('d/m/Y', strtotime($sale['created_at'])) ?><br><br>
                CUIT: 30-12345678-9<br>
                Ingresos Brutos: 30-12345678-9<br>
                Inicio de Actividades: 01/01/2020
            </div>
        </div>
    </div>

    <!-- Customer Data -->
    <div class="afip-border customer-box">
        <div class="customer-left">
            <strong>Nombre:</strong> <?= !empty($sale['cuit_cliente']) ? 'Cliente Registrado' : 'Consumidor Final' ?><br>
            <strong>Domicilio:</strong> A convenir<br>
            <strong>Cond. IVA:</strong> Consumidor Final<br>
            <strong>Cond. Venta:</strong> Contado
        </div>
        <div class="customer-right">
            <strong>CUIT/DNI:</strong> <?= !empty($sale['cuit_cliente']) ? htmlspecialchars($sale['cuit_cliente']) : '00000000' ?><br>
            <strong>Localidad:</strong> CABA<br>
            <strong>Provincia:</strong> Buenos Aires<br>
            <strong>Teléfono:</strong> -
        </div>
    </div>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 15%">Código</th>
                <th style="width: 45%">Descripción</th>
                <th style="width: 10%">Cantidad</th>
                <th style="width: 15%">P. Unitario</th>
                <th style="width: 15%">Importe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $d): ?>
            <tr>
                <td class="text-center"><?= str_pad($d['product_id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td class="text-center"><?= $d['quantity'] ?></td>
                <td class="text-end"><?= number_format($d['unit_price'], 2, ',', '.') ?></td>
                <td class="text-end"><?= number_format($d['subtotal'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Empty space to mimic full height table -->
    <div class="items-container"></div>

    <!-- Totals -->
    <div class="afip-border totals-box" style="border-top: none;">
        <div class="totals-grid">
            <div>Subtotal: $</div>
            <div><?= number_format($sale['total'], 2, ',', '.') ?></div>
            
            <div>Dto./Recargo: $</div>
            <div>0,00</div>
            
            <div>Total: $</div>
            <div><?= number_format($sale['total'], 2, ',', '.') ?></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="afip-border footer-box">
        <!-- Generador de QR simple (Placeholder para visualización) -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode('https://www.afip.gob.ar/fe/qr/?p=cuit') ?>" class="footer-qr" alt="QR AFIP">
        
        <div>
            <div class="footer-arca-logo">ARCA</div>
            <div class="footer-arca-sub">Agencia de Recaudación<br>y Control Aduanero</div>
            <div class="mt-2" style="font-size: 11px; font-weight: bold;">Comprobante Autorizado</div>
            <div style="font-size: 8px; color: #555;">Esta Administración Federal no se responsabiliza por los datos ingresados en el detalle de la operación</div>
        </div>

        <div class="footer-cae">
            CAE N°: <?= !empty($sale['cae']) ? htmlspecialchars($sale['cae']) : 'PENDIENTE' ?><br>
            Fecha de Vto. de CAE: <?= !empty($sale['vto_cae']) ? date('d/m/Y', strtotime($sale['vto_cae'])) : 'PENDIENTE' ?>
        </div>
    </div>

</div>

<script>
function generatePDF() {
    const element = document.getElementById('invoiceArea');
    const opt = {
        margin:       0.2,
        filename:     'factura_<?= str_pad($sale['id'], 8, '0', STR_PAD_LEFT) ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 3, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>

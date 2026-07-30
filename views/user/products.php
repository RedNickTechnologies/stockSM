<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Catálogo de Productos Disponibles</h2>
        <button class="btn btn-outline-danger" onclick="exportPDF()">
            <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
        </button>
    </div>

    <div class="card shadow-sm" id="productsTableContainer">
        <div class="card-body">
            <h4 class="d-none text-center mb-4" id="pdfTitle">Catálogo de Productos - SuperMarket</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio Unitario</th>
                            <th>Stock Disponible</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>#<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <?php if($p['image_url']): ?>
                                    <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div class="bg-light text-center text-muted" style="width: 50px; height: 50px; line-height: 50px; border-radius: 5px;"><i class="bi bi-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td>
                                <?php if($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger fs-6">Agotado</span>
                                <?php elseif($p['stock'] <= $p['stock_min']): ?>
                                    <span class="badge bg-warning text-dark fs-6"><?= $p['stock'] ?> (Poco)</span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($products)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No hay productos activos en este momento.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function exportPDF() {
    const element = document.getElementById('productsTableContainer');
    
    const opt = {
        margin:       0.5,
        filename:     'catalogo_productos.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    document.getElementById('pdfTitle').classList.remove('d-none');
    
    html2pdf().set(opt).from(element).save().then(() => {
        document.getElementById('pdfTitle').classList.add('d-none');
    });
}
</script>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Productos</h2>
        <div>
            <!-- PDF Export Button -->
            <button class="btn btn-outline-danger me-2" onclick="exportPDF()">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <!-- The table container that will be exported -->
    <div class="card shadow-sm" id="productsTableContainer">
        <div class="card-body">
            <h4 class="d-none text-center mb-4" id="pdfTitle">Catálogo de Productos - SuperMarket</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock Actual</th>
                            <th>Estado</th>
                            <th class="no-export text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
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
                                <?php if($p['stock'] <= $p['stock_min']): ?>
                                    <span class="badge bg-danger fs-6"><?= $p['stock'] ?> (Bajo)</span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $p['is_active'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                            </td>
                            <td class="no-export text-end">
                                <form action="index.php?page=admin_products" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $p['is_active'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $p['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <i class="bi <?= $p['is_active'] ? 'bi-lock' : 'bi-unlock' ?>"></i>
                                        <?= $p['is_active'] ? 'Inhabilitar' : 'Habilitar' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Product -->
<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_products" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Unitario ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Inicial</label>
                        <input type="number" name="stock" class="form-control" required value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subir Imagen Local</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">O URL de Imagen (opcional)</label>
                        <input type="url" name="image_link" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
                        <small class="text-muted">Si subes un archivo, este campo será ignorado.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportPDF() {
    const element = document.getElementById('productsTableContainer');
    
    const opt = {
        margin:       0.5,
        filename:     'lista_productos.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    document.querySelectorAll('.no-export').forEach(el => el.style.display = 'none');
    document.getElementById('pdfTitle').classList.remove('d-none');
    
    html2pdf().set(opt).from(element).save().then(() => {
        document.querySelectorAll('.no-export').forEach(el => el.style.display = '');
        document.getElementById('pdfTitle').classList.add('d-none');
    });
}
</script>

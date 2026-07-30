<div class="container mt-4">
    <h2>Generar Nueva Factura</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="index.php?page=user_sale" method="POST" id="saleForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div id="product-lines">
                    <div class="row mb-3 product-line">
                        <div class="col-md-7">
                            <label class="form-label">Producto</label>
                            <select name="products[]" class="form-select product-select" required>
                                <option value="" data-price="0">Seleccionar producto...</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">
                                        <?= htmlspecialchars($p['name']) ?> - $<?= number_format($p['price'], 2) ?> (Stock: <?= $p['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="quantities[]" class="form-control quantity-input" min="1" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger remove-line" disabled><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-secondary btn-sm" id="addLineBtn"><i class="bi bi-plus"></i> Agregar Otro Producto</button>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-3 border rounded">
                    <h4 class="mb-0 text-success fw-bold">Total Estimado: $<span id="totalDisplay">0.00</span></h4>
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-send"></i> Enviar Factura a Validación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('product-lines');
    const addBtn = document.getElementById('addLineBtn');
    const totalDisplay = document.getElementById('totalDisplay');

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-line').forEach(line => {
            const select = line.querySelector('.product-select');
            const qty = line.querySelector('.quantity-input').value;
            const price = select.options[select.selectedIndex]?.dataset.price || 0;
            if (qty > 0) {
                total += (price * qty);
            }
        });
        totalDisplay.innerText = total.toFixed(2);
    }

    container.addEventListener('change', calculateTotal);
    container.addEventListener('input', calculateTotal);

    addBtn.addEventListener('click', function() {
        const firstLine = container.querySelector('.product-line');
        const newLine = firstLine.cloneNode(true);
        newLine.querySelector('.product-select').selectedIndex = 0;
        newLine.querySelector('.quantity-input').value = '';
        newLine.querySelector('.remove-line').disabled = false;
        
        newLine.querySelector('.remove-line').addEventListener('click', function() {
            newLine.remove();
            calculateTotal();
        });
        
        container.appendChild(newLine);
    });
});
</script>

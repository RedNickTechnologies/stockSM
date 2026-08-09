<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-gear"></i> Configuración del Sistema</h2>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> Configuración guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Datos de Facturación (ARCA / AFIP)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        <i class="bi bi-info-circle"></i> Estos datos se utilizarán para generar el membrete de las facturas en PDF. 
                        Actualmente el sistema se encuentra en <strong>Modo Desarrollo (Mock)</strong>.
                    </p>

                    <form action="index.php?page=admin_settings" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="save_arca_settings">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Razón Social</label>
                                <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($all_settings['company_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CUIT</label>
                                <input type="text" name="company_cuit" class="form-control" value="<?= htmlspecialchars($all_settings['company_cuit'] ?? '') ?>" placeholder="Ej: 30-12345678-9" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Domicilio Comercial</label>
                            <input type="text" name="company_address" class="form-control" value="<?= htmlspecialchars($all_settings['company_address'] ?? '') ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Condición frente al IVA</label>
                                <select name="company_vat" class="form-select" required>
                                    <option value="Responsable Inscripto" <?= ($all_settings['company_vat'] ?? '') === 'Responsable Inscripto' ? 'selected' : '' ?>>Responsable Inscripto</option>
                                    <option value="Monotributo" <?= ($all_settings['company_vat'] ?? '') === 'Monotributo' ? 'selected' : '' ?>>Monotributo</option>
                                    <option value="Exento" <?= ($all_settings['company_vat'] ?? '') === 'Exento' ? 'selected' : '' ?>>Exento</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ingresos Brutos</label>
                                <input type="text" name="company_iibb" class="form-control" value="<?= htmlspecialchars($all_settings['company_iibb'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Inicio de Actividades</label>
                                <input type="text" name="company_start_date" class="form-control" value="<?= htmlspecialchars($all_settings['company_start_date'] ?? '') ?>" placeholder="Ej: 01/01/2020">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Soporte y Mesa de Ayuda</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
            <i class="bi bi-envelope-plus"></i> Crear Nuevo Ticket
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Ticket enviado correctamente. El administrador te responderá pronto.</div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($tickets as $t): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 <?= $t['status'] === 'answered' ? 'border-success' : 'border-danger' ?>">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">#<?= $t['id'] ?> - <?= htmlspecialchars($t['subject']) ?></h5>
                    <?php if($t['status'] === 'open'): ?>
                        <span class="badge bg-danger">Abierto (Esperando respuesta)</span>
                    <?php elseif($t['status'] === 'answered'): ?>
                        <span class="badge bg-success">Respondido</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Enviado el: <?= $t['created_at'] ?></p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($t['message'])) ?></p>
                    
                    <?php if($t['admin_reply']): ?>
                        <hr>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <strong class="text-success"><i class="bi bi-person-badge"></i> Respuesta del Administrador:</strong>
                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($t['admin_reply'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($tickets)): ?>
            <div class="col-12"><div class="alert alert-info">No has enviado ningún ticket.</div></div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal New Ticket -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=<?= $_SESSION['role'] === 'transporter' ? 'transporter_tickets' : 'user_tickets' ?>" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Abrir Ticket de Soporte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Asunto</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Ej: Problema con el sistema, Duda de envío...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensaje</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Describe detalladamente tu situación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

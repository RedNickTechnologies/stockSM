<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Soporte y Tickets</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="bi bi-plus-circle"></i> Crear Nuevo Ticket
        </button>
    </div>

    <div class="card shadow-sm border-primary">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="fw-bold">#<?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['subject']) ?></td>
                            <td>
                                <?php if($t['status'] === 'open'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Abierto</span>
                                <?php elseif($t['status'] === 'answered'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-chat-dots"></i> Respondido</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-check2-circle"></i> Cerrado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewTicketModal<?= $t['id'] ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </button>
                            </td>
                        </tr>

                        <!-- View Ticket Modal -->
                        <div class="modal fade" id="viewTicketModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title">Ticket #<?= $t['id'] ?>: <?= htmlspecialchars($t['subject']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h6 class="text-muted mb-1">Tu Mensaje:</h6>
                                        <div class="p-3 bg-light border rounded mb-3">
                                            <?= nl2br(htmlspecialchars($t['message'])) ?>
                                        </div>
                                        
                                        <h6 class="text-muted mb-1">Respuesta del Administrador:</h6>
                                        <?php if($t['admin_reply']): ?>
                                            <div class="p-3 bg-primary bg-opacity-10 border border-primary rounded text-dark">
                                                <?= nl2br(htmlspecialchars($t['admin_reply'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary text-center fst-italic">
                                                Aún no hay respuesta del administrador.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>
                        <?php if (empty($tickets)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No has enviado ningún ticket.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=user_tickets" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Abrir Nuevo Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="create_ticket">
                    
                    <div class="mb-3">
                        <label class="form-label">Asunto (Breve)</label>
                        <input type="text" name="subject" class="form-control" required maxlength="150" placeholder="Ej: Problema al cargar venta">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detalle del reclamo / consulta</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Describe tu problema con detalle..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Enviar Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

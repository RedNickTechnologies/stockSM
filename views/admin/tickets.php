<div class="container mt-4">
    <h2 class="mb-4">Mesa de Ayuda (Tickets de Soporte)</h2>

    <div class="card shadow-sm border-info">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Ticket</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="fw-bold">#<?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['username']) ?></td>
                            <td>
                                <?php 
                                    if($t['role'] === 'user') echo '<span class="badge bg-secondary">Vendedor</span>';
                                    elseif($t['role'] === 'transporter') echo '<span class="badge bg-warning text-dark">Transportista</span>';
                                    else echo '<span class="badge bg-dark">'.$t['role'].'</span>';
                                ?>
                            </td>
                            <td><?= htmlspecialchars($t['subject']) ?></td>
                            <td>
                                <?php if($t['status'] === 'open'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Abierto (Pendiente)</span>
                                <?php elseif($t['status'] === 'answered'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-chat-dots"></i> Respondido</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="bi bi-check2-circle"></i> Cerrado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?= $t['id'] ?>">
                                    <i class="bi bi-reply"></i> Responder
                                </button>
                                <?php if($t['status'] !== 'closed'): ?>
                                <form action="index.php?page=admin_tickets" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="close_ticket">
                                    <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Cerrar este ticket?');">
                                        <i class="bi bi-x-circle"></i> Cerrar
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Modal Responder -->
                        <div class="modal fade text-start" id="replyModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="index.php?page=admin_tickets" method="POST">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title">Ticket #<?= $t['id'] ?> - <?= htmlspecialchars($t['subject']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="reply_ticket">
                                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">

                                            <div class="mb-3">
                                                <h6 class="text-muted">Mensaje del Usuario (<?= htmlspecialchars($t['username']) ?>):</h6>
                                                <div class="p-3 bg-light border rounded">
                                                    <?= nl2br(htmlspecialchars($t['message'])) ?>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tu Respuesta:</label>
                                                <textarea name="admin_reply" class="form-control" rows="5" required placeholder="Escribe tu respuesta aquí..."><?= htmlspecialchars($t['admin_reply'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Guardar Respuesta</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($tickets)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No hay tickets de soporte activos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

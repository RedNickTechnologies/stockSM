<div class="container mt-4">
    <h2>Bandeja de Soporte (Tickets)</h2>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($t['username']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($t['email'] ?? 'Sin email') ?></small>
                                <span class="badge bg-secondary ms-1"><?= $t['role'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($t['subject']) ?></td>
                            <td>
                                <?php if($t['status'] === 'open'): ?>
                                    <span class="badge bg-danger">Abierto</span>
                                <?php elseif($t['status'] === 'answered'): ?>
                                    <span class="badge bg-success">Respondido</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cerrado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ticketModal<?= $t['id'] ?>">Ver / Responder</button>
                            </td>
                        </tr>

                        <!-- Modal for Ticket -->
                        <div class="modal fade" id="ticketModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title">Ticket #<?= $t['id'] ?> - <?= htmlspecialchars($t['subject']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 p-3 bg-light rounded">
                                            <strong>Mensaje del usuario:</strong>
                                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($t['message'])) ?></p>
                                        </div>
                                        
                                        <?php if($t['admin_reply']): ?>
                                        <div class="mb-3 p-3 bg-success bg-opacity-10 rounded border border-success">
                                            <strong>Respuesta anterior:</strong>
                                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($t['admin_reply'])) ?></p>
                                        </div>
                                        <?php endif; ?>

                                        <form action="index.php?page=admin_tickets" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="reply">
                                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Escribir Respuesta</label>
                                                <textarea name="reply" class="form-control" rows="4" required placeholder="Escribe tu respuesta aquí..."></textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-success">Enviar Respuesta y Marcar como Resuelto</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($tickets)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay tickets en la bandeja.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    </div> <!-- Cierre de .main-content -->
</div> <!-- Cierre de .app-container -->
<?php else: ?>
    </div> <!-- Cierre de .login-wrapper -->
<?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2pdf para generación de PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>

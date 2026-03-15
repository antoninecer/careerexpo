    </div>
    <footer class='footer mt-auto py-3 bg-light border-top'>
        <div class='container text-center'>
            <span class='text-muted'>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Všechna práva vyhrazena.</span>
        </div>
    </footer>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>

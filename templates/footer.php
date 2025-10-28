
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="bi bi-code-slash me-2"></i>Desarrollado por <strong>Rubén Marañón</strong></span>
                </div>
                <div class="col-md-6 text-end">
                    <span>&copy; 2025 - Liga de Fútbol</span>
                </div>
            </div>
        </div>
    </footer>

    <?php 
    $jsPath = (strpos($_SERVER['REQUEST_URI'], '/app/') !== false) ? '../assets/js/' : 'assets/js/';
    ?>
    <script src="<?= $jsPath ?>bootstrap.bundle.min.js"></script>

</body>
</html>
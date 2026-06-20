</main>
<footer>
    <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 60px;">
        <span>&copy; <?= $year; ?> All Rights Reserved — UPDS &nbsp;|&nbsp; v<?= env('APP_VERSION', '1.0.0') ?></span>
    </div>
</footer>

<script src="<?= APP_URL ?>/js/popper.min.js"></script>
<script src="<?= APP_URL ?>/js/bootstrap.min.js"></script>
<?php if ($useDataTables ?? false): ?>
    <script src="<?= APP_URL ?>/DataTables/jquery.dataTables.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/dataTables.bootstrap4.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/dataTables.responsive.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/responsive.bootstrap4.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/dataTables.buttons.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/buttons.bootstrap4.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/jszip.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/pdfmake.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/vfs_fonts.js"></script>
    <script src="<?= APP_URL ?>/DataTables/buttons.html5.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/buttons.print.min.js"></script>
    <script src="<?= APP_URL ?>/DataTables/buttons.colVis.min.js"></script>
<?php endif; ?>
<?php foreach ($pageScripts ?? [] as $script): ?>
    <script src="<?= APP_URL . '/' . ltrim($script, '/') ?>"></script>
<?php endforeach; ?>

<?php include __DIR__ . '/messages.php'; ?>

</html>
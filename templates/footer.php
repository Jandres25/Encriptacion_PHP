</main>
<footer>
    <!-- place footer here -->
    <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 60px;">
        <b5>&copy; All Rights Reserved UPDS | <?php echo $year ?>
            <b5 />
    </div>
</footer>

<!-- Libraries -->
<script src="<?= APP_URL ?>/public/js/jquery.min.js"></script>
<script src="<?= APP_URL ?>/public/js/popper.min.js"></script>
<script src="<?= APP_URL ?>/public/js/bootstrap.min.js"></script>
<script src="<?= APP_URL ?>/public/js/fontawesome.js"></script>
<script src="<?= APP_URL ?>/public/DataTables/datatables.js"></script>

<script>
    $(document).ready(function() {
        $("#tabla_id").DataTable({
            "pageLength": 5,
            lengthMenu: [
                [3, 5, 10, 25, 50],
                [3, 5, 10, 25, 50]
            ],
        });
    });
</script>

</html>
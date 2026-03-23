</main>
<footer>
    <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 60px;">
        <span>&copy; <?= $year; ?> All Rights Reserved — UPDS</span>
    </div>
</footer>

<!-- Libraries -->
<script src="<?= APP_URL ?>/public/js/jquery.min.js"></script>
<script src="<?= APP_URL ?>/public/js/popper.min.js"></script>
<script src="<?= APP_URL ?>/public/js/bootstrap.min.js"></script>
<script src="<?= APP_URL ?>/public/DataTables/datatables.js"></script>

<script>
    $(document).ready(function() {
        if ($('#tabla_id').length) {
            $('#tabla_id').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, 'All']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }],
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ users',
                    infoEmpty: 'No users found',
                    infoFiltered: '(filtered from _MAX_ total users)',
                    emptyTable: 'No users available',
                    zeroRecords: 'No matching users found',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                initComplete: function() {
                    $(this.api().table().node()).css('visibility', 'visible');
                }
            });
        }
    });
</script>

</html>
$(document).ready(function () {
    if (!$('#tabla_id').length) {
        return;
    }

    $('#tabla_id').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        initComplete: function () {
            $(this.api().table().node()).css('visibility', 'visible');
        }
    });
});

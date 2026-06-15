$(document).ready(function () {
    if (!$('#activity_log_table').length) {
        return;
    }

    $('#activity_log_table').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'All']
        ],
        order: [[0, 'desc']],
        initComplete: function () {
            $(this.api().table().node()).css('visibility', 'visible');
        }
    });
});

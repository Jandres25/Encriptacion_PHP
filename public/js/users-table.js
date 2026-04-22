$(document).ready(function() {
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
});

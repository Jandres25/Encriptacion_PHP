$(document).ready(function () {
    if (!$('#tabla_id').length) {
        return;
    }

    var today = new Date().toLocaleDateString('es-CO');
    var todayISO = new Date().toISOString().slice(0, 10);

    var table = $('#tabla_id').DataTable({
        responsive: true,
        autoWidth: false,
        buttons: [
            {
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [
                    {
                        text: 'Copiar',
                        extend: 'copy',
                        exportOptions: { columns: ':not(.no-export)' }
                    },
                    {
                        extend: 'pdf',
                        title: 'Usuarios - SecureAuth',
                        filename: 'usuarios_' + todayISO,
                        pageSize: 'LETTER',
                        exportOptions: { columns: ':not(.no-export)' },
                        customize: function (doc) {
                            doc.defaultStyle.fontSize = 10;
                            doc.styles.tableHeader.fontSize = 11;
                            doc.styles.tableHeader.fillColor = '#142e3d';
                            doc.styles.tableHeader.color = '#ffffff';

                            doc.content.splice(0, 1, {
                                text: 'USUARIOS - SECUREAUTH',
                                style: {
                                    fontSize: 16,
                                    alignment: 'center',
                                    bold: true,
                                    margin: [0, 10, 0, 10]
                                }
                            });

                            doc.content.splice(1, 0, {
                                text: 'Listado de usuarios registrados en el sistema',
                                style: {
                                    fontSize: 11,
                                    alignment: 'center',
                                    italic: true,
                                    margin: [0, 0, 0, 10]
                                }
                            });

                            doc.content.splice(2, 0, {
                                text: 'Generado el: ' + new Date().toLocaleString('es-CO'),
                                style: {
                                    fontSize: 9,
                                    alignment: 'right',
                                    margin: [0, 0, 0, 10]
                                }
                            });

                            doc.footer = function (currentPage, pageCount) {
                                return {
                                    columns: [
                                        { text: 'SecureAuth', alignment: 'left', fontSize: 8 },
                                        { text: 'Página ' + currentPage + ' de ' + pageCount, alignment: 'center', fontSize: 8 },
                                        { text: 'Confidencial', alignment: 'right', fontSize: 8 }
                                    ],
                                    margin: [40, 0]
                                };
                            };
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Usuarios - SecureAuth',
                        filename: 'usuarios_' + todayISO,
                        messageTop: 'Registro de usuarios del sistema',
                        messageBottom: 'Documento generado el ' + today,
                        exportOptions: { columns: ':not(.no-export)' }
                    },
                    {
                        extend: 'csv',
                        filename: 'usuarios_' + todayISO,
                        exportOptions: { columns: ':not(.no-export)' }
                    },
                    {
                        extend: 'print',
                        text: 'Imprimir',
                        title: 'Usuarios - SecureAuth',
                        messageTop: 'Reporte generado el ' + today,
                        exportOptions: { columns: ':not(.no-export)' },
                        customize: function (win) {
                            $(win.document.body).find('table')
                                .addClass('table-striped')
                                .css('font-size', '12px');
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: 'Columnas'
            }
        ],
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        initComplete: function () {
            $(this.api().table().node()).css('visibility', 'visible');
        }
    });

    table.buttons().container().appendTo('#tabla_id_wrapper .col-md-6:eq(0)');
});

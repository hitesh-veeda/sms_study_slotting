$(document).ready(function() {
    $('.datatable-search thead tr').clone(true).appendTo( '.datatable-search thead' );
    $('.datatable-search thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text"  placeholder=" '+title+'" />' );
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        });
    });
    var table = $('#datatable-buttons').DataTable({
        
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        orderCellsTop: true,
        fixedHeader: true,
        lengthChange:true,
            "scrollX": true,
            dom: 'Blfrtip',
            buttons:[
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ],
    });
    var table = $('#datatable-activitylist').removeAttr('width').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columnDefs" : [{"targets":[3,4,5,6], "type":"date"}],
        orderCellsTop: true,
        fixedHeader: true,
        lengthChange:true,
        "scrollX": true,
        dom: 'Blfrtip',
        
        buttons:[
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ],
    });

});

var table = $('#datatable-buttons-schedule').DataTable({
    orderCellsTop: true,
    fixedHeader: true,
    lengthChange:!1,
        "pageLength": 50,
        "scrollX": true,
        dom: 'Bfrtip',
        buttons:[
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ]
});

// Designer Brand Commission datatable
$(document).ready(function() {
    $('#commissionDatatable').DataTable({
        dom: 'Bfrtip',
        lengthChange: !1,
        scrollX: true,
        paging: true,
        scrollCollapse: false,
        responsivePriority: 1,
        buttons: [
            {
                extend: 'excel',
                exportOptions: {
                    columns: [ 1, 2, 3],
                    format: {
                        body: function ( inner, rowidx, colidx, node ) {
                            if ($(node).children("input").length > 0) {
                                return $(node).children("input").first().val();
                            } else {
                                return inner;
                            }
                        }
                    }
                }
            },
        ]
    });

});

$(document).ready(function() {
    $('#mytable thead tr').clone(true).appendTo( '#mytable thead' );
    $('#mytable thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder=" Search '+title+'" />' );

        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        });
    });

    var table = $('#mytable').DataTable( {
        orderCellsTop: true,
        fixedHeader: true,
        lengthChange:!1,
        "scrollX": true,
    });

    // set default value in column 1
    table.columns(1).search( "Lili" ).draw();
});
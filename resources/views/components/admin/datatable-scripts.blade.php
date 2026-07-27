@props([
    'tableId',
    'entity' => 'records',
    'orderColumn' => 0,
    'orderDirection' => 'asc',
    'actionColumn' => null,
    'exportFileName' => null,
])

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
(function () {
    if (!window.jQuery || !jQuery.fn.DataTable) return;

    const actionColumn = {{ $actionColumn !== null ? (int) $actionColumn : 'null' }};
    const columnDefs = [];

    if (actionColumn !== null) {
        columnDefs.push({ orderable: false, searchable: false, targets: [actionColumn] });
    }

    const exportColumns = actionColumn !== null
        ? ':not(:eq(' + actionColumn + '))'
        : ':visible';

    const $table = jQuery('#{{ $tableId }}');
    $table.addClass('cell-border row-border');

    $table.DataTable({
        order: [[{{ (int) $orderColumn }}, '{{ $orderDirection }}']],
        pageLength: 10,
        lengthChange: false,
        dom: '<"dt-toolbar"Bf>rt<"dt-footer"ip>',
        buttons: [{
            extend: 'excelHtml5',
            text: 'Export Excel',
            title: null,
            filename: function () {
                return '{{ $exportFileName ?? $entity }}-' + new Date().toISOString().slice(0, 10);
            },
            exportOptions: {
                columns: exportColumns,
                format: {
                    body: function (data) {
                        return jQuery('<div>').html(data).text().trim();
                    }
                }
            }
        }],
        columnDefs: columnDefs,
        language: {
            search: 'Search:',
            info: 'Showing _START_ to _END_ of _TOTAL_ {{ $entity }}',
            infoEmpty: 'No {{ $entity }} available',
            infoFiltered: '(filtered from _MAX_ total {{ $entity }})',
            zeroRecords: 'No matching {{ $entity }} found',
            paginate: {
                previous: 'Previous',
                next: 'Next'
            }
        }
    });
})();
</script>

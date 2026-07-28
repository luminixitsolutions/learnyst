@props([
    'tableId',
    'entity' => 'records',
    'orderColumn' => 0,
    'orderDirection' => 'asc',
    'actionColumn' => null,
    'exportFileName' => null,
    'pageLength' => 10,
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
    const pageLength = {{ (int) $pageLength }};
    const columnDefs = [];
    const narrowHeaders = [
        'status', 'price', 'marks', 'quiz type', 'template', 'duration',
        'active', 'featured', 'visibility', 'role', 'amount', 'date',
        'phone', 'code', 'rating', 'priority', 'section', 'course',
        'punch in', 'punch out', 'hours',
    ];
    let actionsTarget = null;

    const $table = jQuery('#{{ $tableId }}');
    $table.addClass('cell-border row-border');

    $table.find('thead th').each(function (index) {
        const label = jQuery(this).text().trim().toLowerCase().replace(/\s+/g, ' ');

        if (label === 'actions' || label === 'punch') {
            actionsTarget = index;
            jQuery(this).addClass('col-actions');
            columnDefs.push({
                targets: [index],
                orderable: false,
                searchable: false,
                className: 'dt-col-actions text-right',
            });
            return;
        }

        if (narrowHeaders.includes(label)) {
            jQuery(this).addClass('col-narrow');
            columnDefs.push({
                targets: [index],
                className: 'dt-col-narrow',
            });
        }
    });

    if (actionColumn !== null && actionsTarget !== actionColumn) {
        $table.find('thead th').eq(actionColumn).addClass('col-actions');
        columnDefs.push({
            targets: [actionColumn],
            orderable: false,
            searchable: false,
            className: 'dt-col-actions text-right',
        });
    }

    $table.find('tbody tr').each(function () {
        jQuery(this).find('td').each(function (i) {
            const $th = $table.find('thead th').eq(i);
            if ($th.hasClass('col-actions')) {
                jQuery(this).addClass('col-actions');
            }
            if ($th.hasClass('col-narrow')) {
                jQuery(this).addClass('col-narrow');
            }
        });
    });

    const resolvedActionColumn = actionsTarget !== null ? actionsTarget : actionColumn;
    const exportColumns = resolvedActionColumn !== null
        ? ':not(:eq(' + resolvedActionColumn + '))'
        : ':visible';

    const table = $table.DataTable({
        autoWidth: true,
        order: [[{{ (int) $orderColumn }}, '{{ $orderDirection }}']],
        pageLength: pageLength > 0 ? pageLength : 10,
        lengthChange: false,
        paging: pageLength !== -1,
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
        initComplete: function () {
            this.api().columns.adjust();
        },
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

    // Ensure form posts include all rows (not only current DataTables page)
    $table.closest('form').on('submit', function () {
        const $tbody = $table.children('tbody');
        table.rows({ search: 'none' }).every(function () {
            const node = this.node();
            if (node && node.parentNode !== $tbody[0]) {
                $tbody.append(node);
            }
        });
    });
})();
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    .action-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.1rem;
        height: 2.1rem;
        border-radius: .7rem;
        border: 1px solid transparent;
        transition: all .15s ease;
        background: #f8fafc;
        cursor: pointer;
        text-decoration: none;
    }
    .action-icon-btn svg { width: 1rem; height: 1rem; }
    .action-icon-btn--edit { color: var(--theme-accent, #0d9488); border-color: var(--theme-accent-light, #b6dfdb); background: var(--theme-accent-soft, rgba(13, 148, 136, 0.12)); }
    .action-icon-btn--edit:hover { background: rgba(13, 148, 136, 0.18); }
    .action-icon-btn--delete { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
    .action-icon-btn--delete:hover { background: #ffe4e6; }

    .panel-datatable-wrapper table.dataTable,
    .panel-datatable-wrapper table.panel-table {
        table-layout: auto !important;
    }

    /* Actions column — compact, no extra gap */
    .panel-datatable-wrapper .dt-col-actions,
    .panel-datatable-wrapper th.col-actions,
    .panel-datatable-wrapper td.col-actions {
        width: 1%;
        white-space: nowrap !important;
        padding: .5rem .75rem !important;
        text-align: right !important;
        vertical-align: middle !important;
    }

    /* Compact columns — fit content, don't collapse table */
    .panel-datatable-wrapper .dt-col-narrow,
    .panel-datatable-wrapper th.col-narrow,
    .panel-datatable-wrapper td.col-narrow {
        white-space: nowrap !important;
        padding-left: .75rem !important;
        padding-right: .75rem !important;
    }

    /* Hide show-entries dropdown globally */
    .panel-datatable-wrapper .dataTables_length {
        display: none !important;
    }

    /* Toolbar: export left, search right */
    .panel-datatable-wrapper .dataTables_wrapper .dt-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-toolbar .dt-buttons {
        padding: 0;
        margin: 0;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-toolbar .dataTables_filter {
        padding: 0;
        margin: 0;
        float: none;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-toolbar:not(:has(.dt-buttons)) {
        justify-content: flex-end;
    }

    .panel-datatable-wrapper .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: .5rem 1rem;
        background: #fff;
        color: #0f172a;
        outline: none;
        min-width: 220px;
        font-size: .85rem;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--theme-accent, #0d9488);
        box-shadow: 0 0 0 3px var(--theme-accent-soft, rgba(13, 148, 136, 0.12));
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        color: #64748b;
        font-size: .85rem;
    }

    /* Footer: info left, pagination right */
    .panel-datatable-wrapper .dataTables_wrapper .dt-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .85rem 1.25rem;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-footer .dataTables_info {
        padding: 0;
        margin: 0;
        float: none;
        color: #64748b;
        font-size: .85rem;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-footer .dataTables_paginate {
        padding: 0;
        margin: 0;
        float: none;
    }

    /* Export button */
    .panel-datatable-wrapper .dataTables_wrapper .dt-button {
        border: 1px solid var(--theme-accent-soft-border, rgba(13, 148, 136, 0.28)) !important;
        background: var(--theme-accent-soft, rgba(13, 148, 136, 0.12)) !important;
        color: var(--theme-accent-dark, #0b7970) !important;
        border-radius: 9999px !important;
        padding: .5rem 1rem !important;
        font-size: .82rem !important;
        font-weight: 600 !important;
        margin: 0 !important;
        box-shadow: none !important;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dt-button:hover {
        background: rgba(13, 148, 136, 0.18) !important;
        border-color: var(--theme-accent, #0d9488) !important;
        color: var(--theme-accent-deeper, #09655c) !important;
    }

    /* Table borders & styling — override DataTables default minimal borders */
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_scroll,
    .panel-datatable-wrapper .overflow-x-auto {
        padding: 0;
    }
    .panel-datatable-wrapper table.dataTable,
    .panel-datatable-wrapper table.panel-table {
        width: 100% !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0;
        margin: 0 !important;
        background: #fff;
    }
    .panel-datatable-wrapper table.dataTable.no-footer {
        border-bottom: 1px solid #cbd5e1 !important;
    }
    .panel-datatable-wrapper table.dataTable thead th,
    .panel-datatable-wrapper table.dataTable thead td,
    .panel-datatable-wrapper table.panel-table thead th,
    .panel-datatable-wrapper table.panel-table thead td {
        background: #f1f5f9 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: .78rem !important;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: .625rem .875rem !important;
        border-bottom: 1px solid #cbd5e1 !important;
        border-right: 1px solid #cbd5e1 !important;
        border-top: none !important;
        box-sizing: border-box;
        vertical-align: middle;
    }
    .panel-datatable-wrapper table.dataTable tbody th,
    .panel-datatable-wrapper table.dataTable tbody td,
    .panel-datatable-wrapper table.panel-table tbody th,
    .panel-datatable-wrapper table.panel-table tbody td {
        padding: .625rem .875rem !important;
        vertical-align: middle !important;
        color: #475569 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-right: 1px solid #e2e8f0 !important;
        border-top: none !important;
        background: #fff !important;
        box-sizing: border-box;
    }
    .panel-datatable-wrapper table.dataTable thead th:last-child,
    .panel-datatable-wrapper table.dataTable thead td:last-child,
    .panel-datatable-wrapper table.dataTable tbody th:last-child,
    .panel-datatable-wrapper table.dataTable tbody td:last-child,
    .panel-datatable-wrapper table.panel-table thead th:last-child,
    .panel-datatable-wrapper table.panel-table tbody td:last-child {
        border-right: none !important;
    }
    .panel-datatable-wrapper table.dataTable tbody tr:last-child th,
    .panel-datatable-wrapper table.dataTable tbody tr:last-child td,
    .panel-datatable-wrapper table.panel-table tbody tr:last-child td {
        border-bottom: none !important;
    }
    .panel-datatable-wrapper table.dataTable tbody tr:hover th,
    .panel-datatable-wrapper table.dataTable tbody tr:hover td,
    .panel-datatable-wrapper table.panel-table tbody tr:hover td {
        background: var(--theme-accent-soft, rgba(13, 148, 136, 0.06)) !important;
    }
    .panel-datatable-wrapper table.dataTable.stripe tbody tr.odd th,
    .panel-datatable-wrapper table.dataTable.stripe tbody tr.odd td,
    .panel-datatable-wrapper table.dataTable.display tbody tr.odd th,
    .panel-datatable-wrapper table.dataTable.display tbody tr.odd td {
        background: #fff !important;
    }
    .panel-datatable-wrapper table.dataTable.stripe tbody tr.even th,
    .panel-datatable-wrapper table.dataTable.stripe tbody tr.even td,
    .panel-datatable-wrapper table.dataTable.display tbody tr.even th,
    .panel-datatable-wrapper table.dataTable.display tbody tr.even td {
        background: #f8fafc !important;
    }
    .panel-datatable-wrapper table.dataTable.stripe tbody tr:hover th,
    .panel-datatable-wrapper table.dataTable.stripe tbody tr:hover td,
    .panel-datatable-wrapper table.dataTable.display tbody tr:hover th,
    .panel-datatable-wrapper table.dataTable.display tbody tr:hover td {
        background: var(--theme-accent-soft, rgba(13, 148, 136, 0.06)) !important;
    }
    .panel-datatable-wrapper table.dataTable.row-border tbody th,
    .panel-datatable-wrapper table.dataTable.row-border tbody td,
    .panel-datatable-wrapper table.dataTable.display tbody th,
    .panel-datatable-wrapper table.dataTable.display tbody td {
        border-top: none !important;
    }

    /* Round pagination */
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate {
        display: flex;
        align-items: center;
        gap: .25rem;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 9999px !important;
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
        color: #64748b !important;
        min-width: 2.35rem !important;
        height: 2.35rem !important;
        line-height: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: .82rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
        transition: all .15s ease;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--theme-accent, #0d9488) !important;
        color: #fff !important;
        border-color: var(--theme-accent, #0d9488) !important;
        box-shadow: 0 4px 14px var(--theme-accent-glow, rgba(13, 148, 136, 0.28)) !important;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--theme-accent-soft, rgba(13, 148, 136, 0.12)) !important;
        color: var(--theme-accent-dark, #0b7970) !important;
        border-color: var(--theme-accent-soft-border, rgba(13, 148, 136, 0.28)) !important;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: .45;
        cursor: default !important;
        background: #f8fafc !important;
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .panel-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        min-width: auto !important;
        padding: 0 1rem !important;
        border-radius: 9999px !important;
    }
</style>

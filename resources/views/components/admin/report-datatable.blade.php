@props([
    'tableId',
    'hasRecords' => false,
    'entity' => 'records',
    'orderColumn' => 0,
    'orderDirection' => 'desc',
    'actionColumn' => null,
    'exportFileName' => null,
    'emptyTitle' => 'No records found',
    'emptyDescription' => null,
])

<div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
    @if($hasRecords)
        <div class="overflow-x-auto">
            <table id="{{ $tableId }}" class="w-full text-sm panel-table display" style="width:100%">
                {{ $slot }}
            </table>
        </div>
    @else
        <x-empty-state :title="$emptyTitle" :description="$emptyDescription" />
    @endif
</div>

@push('styles')
    <x-admin.datatable-styles />
@endpush

@if($hasRecords)
    @push('scripts')
        <x-admin.datatable-scripts
            :table-id="$tableId"
            :entity="$entity"
            :order-column="$orderColumn"
            :order-direction="$orderDirection"
            :action-column="$actionColumn"
            :export-file-name="$exportFileName ?? $tableId"
        />
    @endpush
@endif

@extends('layouts.app')

@section('title', 'Parent Links')
@section('page-title', 'Parent ↔ Learner Links')
@section('breadcrumb', 'Users / Parent Portal')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-600">Link parent accounts to learners so parents can view attendance, progress, fees, and certificates in the Parent portal.</p>
    </div>

    <form method="POST" action="{{ route('admin.parent-links.store') }}" class="glass-card rounded-2xl p-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="text-xs text-slate-500">Parent</label>
            <select name="parent_user_id" required class="panel-input w-full">
                <option value="">Select parent</option>
                @foreach($parentOptions as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500">Learner</label>
            <select name="learner_user_id" required class="panel-input w-full">
                <option value="">Select learner</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                @endforeach
            </select>
        </div>
        <button class="panel-btn-primary">Create link</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Active links</div>
        @if($links->count())
        <div class="overflow-x-auto">
            <table id="parentLinksTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-3">Parent</th>
                        <th class="px-6 py-3">Learner</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $link)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-3">
                            <div class="font-medium text-slate-800">{{ $link->parent?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $link->parent?->email }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="font-medium text-slate-800">{{ $link->learner?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $link->learner?->email }}</div>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $link->status }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('admin.parent-links.destroy', $link) }}" onsubmit="return confirm('Remove this link?')">@csrf @method('DELETE')
                                <button class="text-xs text-rose-600 font-semibold">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-slate-500">No links yet.</div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Parent accounts</div>
            @if($parents->count())
            <div class="overflow-x-auto">
                <table id="parentsTable" class="w-full text-sm panel-table display" style="width:100%">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Linked learners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parents as $parent)
                        <tr class="hover:bg-indigo-50/40">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $parent->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $parent->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $parent->linkedLearners->count() ? $parent->linkedLearners->pluck('name')->join(', ') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <x-empty-state title="No parent accounts" description="Create users with the Parent role first." />
            @endif
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Available learners</div>
            <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($learners as $learner)
                    <li class="px-6 py-3 text-sm">
                        <span class="font-medium text-slate-800">{{ $learner->name }}</span>
                        <span class="text-slate-500">· {{ $learner->email }}</span>
                    </li>
                @empty
                    <li class="px-6 py-8 text-sm text-slate-500 text-center">No learners in scope.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($links->count())
    <x-admin.datatable-scripts table-id="parentLinksTable" entity="parent links" :order-column="0" order-direction="desc" :action-column="3" export-file-name="parent-links" />
@elseif($parents->count())
    <x-admin.datatable-scripts table-id="parentsTable" entity="parents" :order-column="0" order-direction="asc" export-file-name="parents" />
@endif
@if($links->count() && $parents->count())
<script>
(function () {
    if (!window.jQuery || !jQuery.fn.DataTable) return;
    const $table = jQuery('#parentsTable');
    if (!$table.length || jQuery.fn.DataTable.isDataTable($table)) return;
    $table.addClass('cell-border row-border');
    $table.DataTable({
        autoWidth: true,
        order: [[0, 'asc']],
        pageLength: 10,
        lengthChange: false,
        dom: '<"dt-toolbar"Bf>rt<"dt-footer"ip>',
        buttons: [{
            extend: 'excelHtml5',
            text: 'Export Excel',
            title: null,
            filename: function () {
                return 'parents-' + new Date().toISOString().slice(0, 10);
            },
            exportOptions: {
                columns: ':visible',
                format: {
                    body: function (data) {
                        return jQuery('<div>').html(data).text().trim();
                    }
                }
            }
        }],
        language: {
            search: 'Search:',
            info: 'Showing _START_ to _END_ of _TOTAL_ parents',
            infoEmpty: 'No parents available',
            infoFiltered: '(filtered from _MAX_ total parents)',
            zeroRecords: 'No matching parents found',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });
})();
</script>
@endif
@endpush

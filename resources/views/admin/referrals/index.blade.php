@extends('layouts.app')

@section('title', 'Referral Program')
@section('page-title', 'Referral Program')
@section('breadcrumb', 'Sales / Referrals')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Unique learner codes, attribution, and wallet rewards.</p>
        <a href="{{ route('admin.referrals.settings') }}" class="panel-btn-secondary">Reward Settings</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Codes" :value="number_format($stats['codes'])" />
        <x-stat-card title="Referrals" :value="number_format($stats['total'])" />
        <x-stat-card title="Rewarded" :value="number_format($stats['rewarded'])" />
        <x-stat-card title="Pending" :value="number_format($stats['pending'])" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Generate / Ensure Code</h3>
            <form method="POST" action="{{ route('admin.referrals.codes.store') }}" class="space-y-3">
                @csrf
                <x-form-input label="Learner" name="user_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Custom Code (optional)" name="code" :value="old('code')" />
                <x-form-input label="Max Uses (optional)" name="max_uses" type="number" :value="old('max_uses')" />
                <button type="submit" class="panel-btn-primary">Save Code</button>
            </form>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Apply Referral Code</h3>
            <form method="POST" action="{{ route('admin.referrals.apply') }}" class="space-y-3">
                @csrf
                <x-form-input label="New / Referred Learner" name="referred_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Referral Code" name="referral_code" :value="old('referral_code')" required />
                <button type="submit" class="panel-btn-primary">Apply & Reward</button>
            </form>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Referral Codes</h3>
        </div>
        @if($codes->count())
        <div class="overflow-x-auto">
            <table id="referralCodesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-3">Learner</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Uses</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($codes as $code)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-3 text-slate-800">{{ $code->user?->name }}</td>
                        <td class="px-6 py-3 font-mono text-emerald-700">{{ $code->code }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $code->uses_count }}@if($code->max_uses)/{{ $code->max_uses }}@endif ({{ $code->referrals_count }} referrals)</td>
                        <td class="px-6 py-3"><x-badge :type="$code->is_active ? 'success' : 'danger'">{{ $code->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('admin.referrals.codes.toggle', $code) }}">@csrf
                                <button class="text-sm text-emerald-700 hover:underline">{{ $code->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No referral codes yet." />
        @endif
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-800">Referral Report</h3>
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach(['pending','qualified','rewarded','rejected'] as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button class="panel-btn-secondary">Filter</button>
            </form>
        </div>
        @if($referrals->count())
        <div class="overflow-x-auto">
            <table id="referralsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-3">Referrer</th>
                        <th class="px-6 py-3">Referred</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Rewards</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referrals as $referral)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-3 text-slate-800">{{ $referral->referrer?->name }}</td>
                        <td class="px-6 py-3 text-slate-800">{{ $referral->referred?->name }}</td>
                        <td class="px-6 py-3 font-mono text-emerald-700">{{ $referral->referralCode?->code }}</td>
                        <td class="px-6 py-3 text-slate-600">₹{{ number_format($referral->referrer_reward, 0) }} / ₹{{ number_format($referral->referred_reward, 0) }}</td>
                        <td class="px-6 py-3"><x-badge :type="match($referral->status){'rewarded'=>'success','pending'=>'warning','qualified'=>'info',default=>'danger'}">{{ ucfirst($referral->status) }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-500">{{ $referral->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            @if($referral->status !== 'rewarded')
                            <form method="POST" action="{{ route('admin.referrals.reward', $referral) }}">@csrf
                                <button class="text-sm text-emerald-700 hover:underline">Reward</button>
                            </form>
                            @else
                            <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No referrals recorded yet." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($codes->count())
    <x-admin.datatable-scripts table-id="referralCodesTable" entity="referral codes" :order-column="0" order-direction="desc" :action-column="4" export-file-name="referral-codes" />
    @if($referrals->count())
    <script>
    (function () {
        if (!window.jQuery || !jQuery.fn.DataTable) return;
        if (jQuery.fn.DataTable.isDataTable('#referralsTable')) return;
        const $table = jQuery('#referralsTable');
        $table.addClass('cell-border row-border');
        $table.find('thead th').eq(6).addClass('col-actions');
        $table.DataTable({
            autoWidth: true,
            order: [[5, 'desc']],
            pageLength: 10,
            lengthChange: false,
            dom: '<"dt-toolbar"Bf>rt<"dt-footer"ip>',
            buttons: [{
                extend: 'excelHtml5',
                text: 'Export Excel',
                title: null,
                filename: function () {
                    return 'referrals-' + new Date().toISOString().slice(0, 10);
                },
                exportOptions: {
                    columns: ':not(:eq(6))',
                    format: {
                        body: function (data) {
                            return jQuery('<div>').html(data).text().trim();
                        }
                    }
                }
            }],
            columnDefs: [{
                targets: [6],
                orderable: false,
                searchable: false,
                className: 'dt-col-actions text-right',
            }],
            language: {
                search: 'Search:',
                info: 'Showing _START_ to _END_ of _TOTAL_ referrals',
                infoEmpty: 'No referrals available',
                infoFiltered: '(filtered from _MAX_ total referrals)',
                zeroRecords: 'No matching referrals found',
                paginate: { previous: 'Previous', next: 'Next' }
            }
        });
    })();
    </script>
    @endif
@elseif($referrals->count())
    <x-admin.datatable-scripts table-id="referralsTable" entity="referrals" :order-column="5" order-direction="desc" :action-column="6" export-file-name="referrals" />
@endif
@endpush

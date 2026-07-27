<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead>
                <tr class="text-left">
                    <th class="px-6 py-4">Course</th>
                    <th class="px-6 py-4">Certificate No.</th>
                    <th class="px-6 py-4">Issued</th>
                    <th class="px-6 py-4">Expires</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @php $lifecycle = app(\App\Services\CertificateLifecycleService::class); @endphp
                @forelse($certificates as $certificate)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $certificate->course?->title ?? 'Certificate' }}</td>
                        <td class="px-6 py-4 font-mono text-slate-500">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->issued_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$lifecycle->statusBadgeType($certificate->status)">{{ $lifecycle->statusLabel($certificate->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ $downloadRoute($certificate) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100">
                                Download
                            </a>
                            @if($lifecycle->isRenewable($certificate))
                                <a href="{{ $renewRoute($certificate) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100">
                                    Renew
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">No certificates yet. Complete courses to earn certificates.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $certificates->links() }}
</div>

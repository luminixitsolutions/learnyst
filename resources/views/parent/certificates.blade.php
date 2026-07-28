@extends('layouts.app')
@section('title', 'Certificates')
@section('page-title', 'Certificates')
@section('breadcrumb', 'Parent / Certificates')
@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Course</th><th class="px-6 py-3 text-left">Number</th><th class="px-6 py-3 text-left">Issued</th><th></th></tr></thead>
        <tbody>
        @forelse($certificates as $c)
            <tr>
                <td class="px-6 py-3">{{ $c->user?->name }}</td>
                <td class="px-6 py-3">{{ $c->course?->title }}</td>
                <td class="px-6 py-3 font-mono text-xs">{{ $c->certificate_number }}</td>
                <td class="px-6 py-3">{{ $c->issued_at?->format('M d, Y') }}</td>
                <td class="px-6 py-3 text-right space-x-2">
                    <a href="{{ route('certificates.verify', ['number' => $c->certificate_number]) }}" class="text-slate-600 text-xs font-semibold" target="_blank">Verify</a>
                    <a href="{{ route('parent.certificates.download', $c) }}" class="text-indigo-600 text-xs font-semibold">Download</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No certificates yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $certificates->links() }}</div>
</div>
@endsection

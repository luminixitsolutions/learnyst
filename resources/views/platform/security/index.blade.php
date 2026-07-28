@extends('layouts.app')

@section('title', 'Security')
@section('page-title', 'Security')
@section('breadcrumb', 'Platform Admin / System / Security')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">Maintenance flag and optional IP allowlist controls.</p>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @include('platform.security._form', ['security' => $security, 'action' => route('platform.security.update'), 'section' => null])
</div>
@endsection

@extends('layouts.app')

@section('title', $company->name.' Branding')
@section('page-title', $company->name)
@section('breadcrumb', 'Platform / Branding')

@section('content')
<div class="space-y-6 max-w-3xl">
    <a href="{{ route('platform.branding.index') }}" class="text-sm text-emerald-600">← Back</a>
    <div class="bg-white rounded-2xl p-6 shadow-sm space-y-2 text-sm">
        <p><strong>Domain:</strong> {{ $company->custom_domain ?: '—' }}</p>
        <p><strong>Primary:</strong> {{ $company->primary_color ?: '—' }}</p>
        <p><strong>From email:</strong> {{ $company->email_from_name }} &lt;{{ $company->email_from_address }}&gt;</p>
        <p><strong>Verified:</strong> {{ $company->domain_verified_at?->toDateTimeString() ?: 'No' }}</p>
        <form method="POST" action="{{ route('platform.branding.verify', $company) }}">@csrf
            <button class="mt-3 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm">Mark domain verified</button>
        </form>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold mb-3">DNS instructions</h3>
        @foreach($dns as $row)
            <div class="text-xs font-mono py-2 border-b">{{ $row['type'] }} {{ $row['host'] }} → {{ $row['value'] }}</div>
        @endforeach
    </div>
</div>
@endsection

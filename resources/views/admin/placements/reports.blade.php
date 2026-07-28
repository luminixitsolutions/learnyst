@extends('layouts.app')
@section('title', 'Placement Reports')
@section('page-title', 'Placement Reports')
@section('breadcrumb', 'Placements / Reports')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-white mb-3">By status</h3>
        @foreach($byStatus as $st => $n)
            <div class="flex justify-between text-sm py-1 text-slate-300"><span>{{ $st }}</span><span>{{ $n }}</span></div>
        @endforeach
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-white mb-3">Listings</h3>
        @foreach($byType as $t => $n)
            <div class="flex justify-between text-sm py-1 text-slate-300"><span class="capitalize">{{ $t }}</span><span>{{ $n }}</span></div>
        @endforeach
    </div>
    <div class="glass-card rounded-2xl p-6 text-center">
        <p class="text-xs text-slate-400">Hired</p>
        <p class="text-3xl font-bold text-emerald-400 mt-2">{{ $hired }}</p>
    </div>
</div>
@endsection

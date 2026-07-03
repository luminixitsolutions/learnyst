@extends('layouts.app')

@section('title', 'More Products')
@section('page-title', 'More Products')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-8">
    <div>
        <h3 class="text-2xl font-bold text-slate-900">More Products</h3>
        <p class="text-sm text-slate-500 mt-1">Explore additional product types for your academy.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($products as $product)
            <a href="{{ $product['url'] }}"
               class="group glass-card rounded-2xl p-6 flex flex-col gap-4 hover:border-emerald-200 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $product['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">{{ $product['title'] }}</h4>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $product['description'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection

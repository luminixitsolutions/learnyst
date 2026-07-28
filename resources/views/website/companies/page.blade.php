@extends('website.layouts.builder')

@section('title', $page->seo_title ?: $page->title.' | '.$company->name)
@section('meta_description', $page->seo_description)

@section('content')
@php
    $headerMenus = $menus['header'] ?? collect();
    $footerMenus = $menus['footer'] ?? collect();
@endphp
<div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('website.companies.show', $company->slug) }}" class="font-bold text-slate-900 text-lg">{{ $company->name }}</a>
            <nav class="flex flex-wrap gap-4 text-sm">
                @foreach($headerMenus as $item)
                    <a href="{{ $item->resolvedUrl() }}" class="text-slate-600 hover:text-slate-900">{{ $item->label }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main>
        @foreach($page->enabledBlocks as $block)
            @include('website.companies.partials.block', ['block' => $block, 'company' => $company, 'courses' => $courses ?? collect()])
        @endforeach
        @if($page->enabledBlocks->isEmpty())
            <div class="max-w-3xl mx-auto px-4 py-20 text-center text-slate-500">This page has no enabled blocks yet.</div>
        @endif
    </main>

    <footer class="border-t border-slate-200 bg-white mt-12">
        <div class="max-w-5xl mx-auto px-4 py-8 flex flex-wrap gap-4 text-sm text-slate-600">
            @foreach($footerMenus as $item)
                <a href="{{ $item->resolvedUrl() }}" class="hover:text-slate-900">{{ $item->label }}</a>
            @endforeach
            <span class="ml-auto text-slate-400">© {{ date('Y') }} {{ $company->name }}</span>
        </div>
    </footer>
</div>
@endsection

@extends('website.layouts.app')

@section('title', $blog->title . ' – ' . $company->name)
@section('meta_description', $blog->excerpt ?: Str::limit(strip_tags($blog->body ?? ''), 160))

@section('content')
<section class="ly-section">
    <div class="ly-container" style="max-width:860px;">
        <a class="ly-company-back" style="color:#64748b;" href="{{ route('website.companies.show', $company->slug) }}#blogs">← Back to {{ $company->name }}</a>
        <p class="ly-tag" style="margin-top:18px;">{{ $company->name }}</p>
        <h1 style="font-family:Poppins,sans-serif;font-size:clamp(28px,4vw,42px);line-height:1.2;color:#0b1220;margin:8px 0 12px;">{{ $blog->title }}</h1>
        <p style="color:#64748b;margin:0 0 24px;">{{ optional($blog->published_at)->format('M d, Y') }}</p>
        @if($blog->coverUrl())
            <img src="{{ $blog->coverUrl() }}" alt="{{ $blog->title }}" style="width:100%;border-radius:18px;margin-bottom:28px;">
        @endif
        <div class="ly-blog-prose">
            @foreach(preg_split("/\n\s*\n/", trim((string) $blog->body) ?: '') as $para)
                @if(trim($para) !== '')
                    <p>{{ $para }}</p>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endsection

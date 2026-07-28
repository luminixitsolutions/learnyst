@php $c = $block->content ?? []; @endphp
<section class="py-12 md:py-16 {{ $block->block_type === 'hero' ? 'bg-slate-900 text-white' : '' }}">
    <div class="max-w-5xl mx-auto px-4">
        @if($block->block_type === 'hero')
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-3xl md:text-5xl font-bold tracking-tight">{{ $c['headline'] ?? $block->title }}</h1>
                    @if(!empty($c['subheadline']))<p class="mt-4 text-lg text-slate-300">{{ $c['subheadline'] }}</p>@endif
                    @if(!empty($c['cta_label']))
                        <a href="{{ $c['cta_url'] ?? '#' }}" class="inline-flex mt-6 px-5 py-2.5 rounded-xl bg-white text-slate-900 font-semibold text-sm">{{ $c['cta_label'] }}</a>
                    @endif
                </div>
                @if(!empty($c['image_url']))
                    <img src="{{ $c['image_url'] }}" alt="" class="w-full rounded-2xl object-cover max-h-80" />
                @endif
            </div>
        @elseif($block->block_type === 'text')
            <h2 class="text-2xl font-bold text-slate-900 mb-3">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="prose prose-slate max-w-none whitespace-pre-line">{{ $c['body'] ?? '' }}</div>
        @elseif($block->block_type === 'cta')
            <div class="rounded-2xl bg-indigo-600 text-white p-8 md:p-10 text-center">
                <h2 class="text-2xl md:text-3xl font-bold">{{ $c['headline'] ?? $block->title }}</h2>
                @if(!empty($c['subheadline']))<p class="mt-2 text-indigo-100">{{ $c['subheadline'] }}</p>@endif
                @if(!empty($c['cta_label']))
                    <a href="{{ $c['cta_url'] ?? '#' }}" class="inline-flex mt-6 px-5 py-2.5 rounded-xl bg-white text-indigo-700 font-semibold text-sm">{{ $c['cta_label'] }}</a>
                @endif
            </div>
        @elseif($block->block_type === 'faq')
            <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="space-y-3">
                @foreach(($c['items'] ?? []) as $item)
                    <details class="bg-white rounded-xl border border-slate-200 p-4">
                        <summary class="font-medium cursor-pointer">{{ $item['q'] ?? '' }}</summary>
                        <p class="mt-2 text-sm text-slate-600">{{ $item['a'] ?? '' }}</p>
                    </details>
                @endforeach
            </div>
        @elseif($block->block_type === 'testimonials')
            <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach(($c['items'] ?? []) as $item)
                    <blockquote class="bg-white rounded-xl border border-slate-200 p-5">
                        <p class="text-slate-700">“{{ $item['quote'] ?? '' }}”</p>
                        <footer class="mt-3 text-sm font-semibold text-slate-900">{{ $item['name'] ?? '' }}</footer>
                    </blockquote>
                @endforeach
            </div>
        @elseif($block->block_type === 'faculty')
            <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach(($c['items'] ?? []) as $item)
                    <div class="bg-white rounded-xl border border-slate-200 p-5 text-center">
                        <div class="font-semibold">{{ $item['name'] ?? '' }}</div>
                        <div class="text-sm text-slate-500">{{ $item['role'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @elseif($block->block_type === 'gallery')
            <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach(($c['items'] ?? []) as $item)
                    @if(!empty($item['image_url']))
                        <figure>
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['caption'] ?? '' }}" class="rounded-xl w-full object-cover h-40" />
                            @if(!empty($item['caption']))<figcaption class="text-xs text-slate-500 mt-1">{{ $item['caption'] }}</figcaption>@endif
                        </figure>
                    @endif
                @endforeach
            </div>
        @elseif($block->block_type === 'pricing')
            <h2 class="text-2xl font-bold text-slate-900 mb-6 text-center">{{ $c['headline'] ?? $block->title }}</h2>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach(($c['items'] ?? []) as $item)
                    <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
                        <div class="font-semibold">{{ $item['name'] ?? '' }}</div>
                        <div class="text-2xl font-bold mt-2">{{ $item['price'] ?? '' }}</div>
                        <p class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $item['features'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @elseif($block->block_type === 'courses')
            <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $c['headline'] ?? $block->title }}</h2>
            @if(!empty($c['body']))<p class="text-slate-600 mb-6">{{ $c['body'] }}</p>@endif
            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($courses as $course)
                    <a href="{{ route('website.companies.show', $company->slug) }}#courses" class="bg-white rounded-xl border border-slate-200 p-5 hover:border-indigo-300">
                        <div class="font-semibold text-slate-900">{{ $course->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $course->is_free ? 'Free' : '₹'.number_format((float)$course->price,0) }}</div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No published courses yet.</p>
                @endforelse
            </div>
        @elseif($block->block_type === 'form')
            <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $c['headline'] ?? $block->title }}</h2>
            @if(!empty($c['body']))<p class="text-slate-600 mb-4">{{ $c['body'] }}</p>@endif
            @if(session('success'))<p class="mb-3 text-sm text-emerald-700">{{ session('success') }}</p>@endif
            <form method="POST" action="{{ route('website.companies.enquiries.store', $company->slug) }}" class="bg-white rounded-xl border border-slate-200 p-5 space-y-3 max-w-xl">
                @csrf
                <input type="hidden" name="_redirect" value="{{ url()->current() }}" />
                <input name="name" required placeholder="Name" class="w-full rounded-lg border-slate-300 text-sm" value="{{ old('name') }}" />
                <input name="email" type="email" required placeholder="Email" class="w-full rounded-lg border-slate-300 text-sm" value="{{ old('email') }}" />
                <input name="phone" placeholder="Phone" class="w-full rounded-lg border-slate-300 text-sm" value="{{ old('phone') }}" />
                <input name="subject" placeholder="Subject" class="w-full rounded-lg border-slate-300 text-sm" value="{{ old('subject') }}" />
                <textarea name="message" required rows="4" placeholder="Message" class="w-full rounded-lg border-slate-300 text-sm">{{ old('message') }}</textarea>
                <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold">Send enquiry</button>
            </form>
        @elseif($block->block_type === 'newsletter')
            <div class="rounded-2xl bg-slate-900 text-white p-8 text-center">
                <h2 class="text-2xl font-bold">{{ $c['headline'] ?? $block->title }}</h2>
                @if(!empty($c['subheadline']))<p class="mt-2 text-slate-300">{{ $c['subheadline'] }}</p>@endif
                <form method="POST" action="{{ route('website.companies.newsletter.store', $company->slug) }}" class="mt-6 flex flex-col sm:flex-row gap-2 justify-center max-w-md mx-auto">
                    @csrf
                    <input type="hidden" name="_redirect" value="{{ url()->current() }}" />
                    <input name="email" type="email" required placeholder="Email address" class="rounded-lg border-0 text-slate-900 text-sm flex-1" />
                    <button class="px-4 py-2 rounded-lg bg-white text-slate-900 text-sm font-semibold">Subscribe</button>
                </form>
            </div>
        @else
            <h2 class="text-2xl font-bold text-slate-900">{{ $block->title }}</h2>
        @endif
    </div>
</section>

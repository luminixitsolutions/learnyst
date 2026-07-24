@extends('layouts.app')

@section('title', 'Institute Profile')
@section('page-title', 'Institute Profile')
@section('breadcrumb', 'Settings / Institute Profile')

@section('content')
@php
    $social = old('social_links', $company->social_links ?? []);
    $profile = old('profile', $company->profile ?? []);
    $highlightsText = old('highlights', implode("\n", $company->highlights ?? []));
    $specialtiesText = old('profile.specialties', implode("\n", $profile['specialties'] ?? []));
    $stats = old('profile.stats', $profile['stats'] ?? [['label' => '', 'value' => ''], ['label' => '', 'value' => ''], ['label' => '', 'value' => ''], ['label' => '', 'value' => '']]);
    $whyUs = old('profile.why_us', $profile['why_us'] ?? [
        ['title' => '', 'text' => '', 'icon' => 'fa-graduation-cap'],
        ['title' => '', 'text' => '', 'icon' => 'fa-users'],
        ['title' => '', 'text' => '', 'icon' => 'fa-laptop'],
        ['title' => '', 'text' => '', 'icon' => 'fa-certificate'],
    ]);
    $faqs = old('profile.faqs', $profile['faqs'] ?? [['q' => '', 'a' => ''], ['q' => '', 'a' => ''], ['q' => '', 'a' => '']]);
    while (count($stats) < 4) { $stats[] = ['label' => '', 'value' => '']; }
    while (count($whyUs) < 4) { $whyUs[] = ['title' => '', 'text' => '', 'icon' => 'fa-check-circle']; }
    while (count($faqs) < 3) { $faqs[] = ['q' => '', 'a' => '']; }
@endphp

<div class="space-y-6 max-w-5xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">Update your public institute profile basics, branding, and story.</p>
            <p class="text-xs text-slate-400 mt-1">Public URL: <a href="{{ $publicUrl }}" target="_blank" class="text-indigo-600 hover:underline">{{ $publicUrl }}</a></p>
        </div>
        <a href="{{ $publicUrl }}" target="_blank" class="panel-btn-secondary">Preview profile</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.company-page.testimonials') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Testimonials</a>
        <a href="{{ route('admin.company-page.reviews') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Reviews</a>
        <a href="{{ route('admin.company-page.gallery') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Gallery</a>
        <a href="{{ route('admin.company-page.videos') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Videos</a>
        <a href="{{ route('admin.company-page.blogs') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Blogs</a>
        <a href="{{ route('admin.company-page.team') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Team</a>
        <a href="{{ route('admin.company-page.enquiries') }}" class="glass-card rounded-xl p-4 text-sm font-semibold text-slate-700 hover:text-indigo-600">Enquiries</a>
    </div>

    <form method="POST" action="{{ route('admin.company-profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Basics</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Institute name" name="name" :value="old('name', $company->name)" required />
                <x-form-input label="Profile slug" name="slug" :value="old('slug', $company->slug)" placeholder="my-academy" />
            </div>
            <x-form-input label="Tagline" name="tagline" :value="old('tagline', $company->tagline)" placeholder="The most trusted exam prep academy" />
            <x-form-input label="About (use blank lines for paragraphs)" name="about" type="textarea" :value="old('about', $company->about)" />
            <x-form-input label="Highlights (one per line)" name="highlights" type="textarea" :value="$highlightsText" />
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_public" value="1" class="rounded border-slate-300 text-indigo-600" @checked(old('is_public', $company->is_public))>
                Show this company in the public directory
            </label>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Mission, vision & facts</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Mission" name="profile[mission]" type="textarea" :value="$profile['mission'] ?? ''" />
                <x-form-input label="Vision" name="profile[vision]" type="textarea" :value="$profile['vision'] ?? ''" />
                <x-form-input label="Founded year" name="profile[founded_year]" :value="$profile['founded_year'] ?? ''" placeholder="2018" />
                <x-form-input label="Working hours" name="profile[working_hours]" :value="$profile['working_hours'] ?? ''" placeholder="Mon–Sat, 9:00 AM – 6:00 PM" />
                <x-form-input label="State" name="profile[state]" :value="$profile['state'] ?? ''" />
                <x-form-input label="Country" name="profile[country]" :value="$profile['country'] ?? 'India'" />
            </div>
            <x-form-input label="Specialties (one per line)" name="profile[specialties]" type="textarea" :value="$specialtiesText" />
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Stats</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($stats as $i => $stat)
                    <div class="grid grid-cols-2 gap-3">
                        <x-form-input label="Stat value" :name="'profile[stats]['.$i.'][value]'" :value="$stat['value'] ?? ''" placeholder="12,000+" />
                        <x-form-input label="Stat label" :name="'profile[stats]['.$i.'][label]'" :value="$stat['label'] ?? ''" placeholder="Learners" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Why choose us</h3>
            @foreach($whyUs as $i => $item)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 border border-slate-100 rounded-xl p-4">
                    <x-form-input label="Icon class" :name="'profile[why_us]['.$i.'][icon]'" :value="$item['icon'] ?? 'fa-check-circle'" placeholder="fa-graduation-cap" />
                    <x-form-input label="Title" :name="'profile[why_us]['.$i.'][title]'" :value="$item['title'] ?? ''" />
                    <x-form-input label="Text" :name="'profile[why_us]['.$i.'][text]'" :value="$item['text'] ?? ''" />
                </div>
            @endforeach
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Branding</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Logo</label>
                    @if($company->logoUrl())
                        <img src="{{ $company->logoUrl() }}" alt="" class="h-20 rounded-xl border border-slate-200 object-contain bg-slate-50 p-2">
                        <label class="inline-flex items-center gap-2 text-xs text-red-600">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300"> Remove logo
                        </label>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="panel-input">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Cover image</label>
                    @if($company->coverUrl())
                        <img src="{{ $company->coverUrl() }}" alt="" class="h-20 w-full rounded-xl border border-slate-200 object-cover">
                        <label class="inline-flex items-center gap-2 text-xs text-red-600">
                            <input type="checkbox" name="remove_cover" value="1" class="rounded border-slate-300"> Remove cover
                        </label>
                    @endif
                    <input type="file" name="cover_image" accept="image/*" class="panel-input">
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">FAQs</h3>
            @foreach($faqs as $i => $faq)
                <div class="grid grid-cols-1 gap-3 border border-slate-100 rounded-xl p-4">
                    <x-form-input label="Question" :name="'profile[faqs]['.$i.'][q]'" :value="$faq['q'] ?? ''" />
                    <x-form-input label="Answer" :name="'profile[faqs]['.$i.'][a]'" type="textarea" :value="$faq['a'] ?? ''" />
                </div>
            @endforeach
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Contact</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Email" name="email" type="email" :value="old('email', $company->email)" />
                <x-form-input label="Phone" name="phone" :value="old('phone', $company->phone)" />
                <x-form-input label="Website URL" name="website_url" :value="old('website_url', $company->website_url)" placeholder="https://" />
                <x-form-input label="City" name="city" :value="old('city', $company->city)" />
            </div>
            <x-form-input label="Address" name="address" type="textarea" :value="old('address', $company->address)" />
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Social links</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn', 'twitter' => 'Twitter / X', 'telegram' => 'Telegram'] as $key => $label)
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">{{ $label }}</label>
                        <input type="url" name="social_links[{{ $key }}]" value="{{ $social[$key] ?? '' }}" class="panel-input" placeholder="https://">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="panel-btn-primary">Save institute profile</button>
            <a href="{{ $publicUrl }}" target="_blank" class="text-sm text-slate-500 hover:text-slate-700">View public page</a>
        </div>
    </form>
</div>
@endsection

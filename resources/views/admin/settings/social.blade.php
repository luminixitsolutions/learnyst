@extends('layouts.app')

@section('title', 'Social Links')
@section('page-title', 'Social Links')
@section('breadcrumb', 'Settings / Social Media')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Configure social media and website URLs displayed on your platform.</p>
        <a href="{{ route('admin.settings.index') }}" class="text-sm text-slate-500 hover:text-white">← All Settings</a>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.settings.social.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            @foreach([
                'facebook' => 'Facebook URL',
                'youtube' => 'YouTube URL',
                'linkedin' => 'LinkedIn URL',
                'telegram' => 'Telegram URL',
                'whatsapp' => 'WhatsApp URL',
                'instagram' => 'Instagram URL',
                'website' => 'Website URL',
            ] as $key => $label)
            <x-form-input :label="$label" :name="$key" type="url" :value="old($key, $links[$key] ?? '')" placeholder="https://" />
            @endforeach
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.settings.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Social Links</button>
            </div>
        </form>
    </div>
</div>
@endsection

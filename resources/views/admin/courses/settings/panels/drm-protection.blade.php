<?php
$drm = $settings->drm_config ?? [];
?>
<div class="space-y-5">
    <p class="text-sm text-slate-500">
        Practical browser protections only — signed URLs, watermark overlay, device/session limits.
        This is not uncrackable DRM; determined users can still capture screen content.
    </p>

    <label class="flex items-center gap-3">
        <input type="hidden" name="enabled" value="0">
        <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $drm['enabled'] ?? false)) class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Enable content protection for this course</span>
    </label>

    <label class="flex items-center gap-3">
        <input type="hidden" name="signed_urls" value="0">
        <input type="checkbox" name="signed_urls" value="1" @checked(old('signed_urls', $drm['signed_urls'] ?? true)) class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Use signed / expiring media URLs</span>
    </label>

    <label class="flex items-center gap-3">
        <input type="hidden" name="watermark" value="0">
        <input type="checkbox" name="watermark" value="1" @checked(old('watermark', $drm['watermark'] ?? true)) class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Show learner watermark on player</span>
    </label>

    <label class="flex items-center gap-3">
        <input type="hidden" name="restrict_seeking" value="0">
        <input type="checkbox" name="restrict_seeking" value="1" @checked(old('restrict_seeking', $drm['restrict_seeking'] ?? false)) class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Restrict seeking (hint for player)</span>
    </label>

    <label class="flex items-center gap-3">
        <input type="hidden" name="block_download" value="0">
        <input type="checkbox" name="block_download" value="1" @checked(old('block_download', $drm['block_download'] ?? true)) class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Discourage download (controlsList)</span>
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-input label="Signed URL TTL (minutes)" name="url_ttl_minutes" type="number" :value="old('url_ttl_minutes', $drm['url_ttl_minutes'] ?? 60)" />
        <x-form-input label="Max devices" name="max_devices" type="number" :value="old('max_devices', $drm['max_devices'] ?? 3)" />
        <x-form-input label="Max parallel streams" name="max_parallel_sessions" type="number" :value="old('max_parallel_sessions', $drm['max_parallel_sessions'] ?? 1)" />
        <x-form-input label="Max watch seconds / day (optional)" name="max_watch_seconds_per_day" type="number" :value="old('max_watch_seconds_per_day', $drm['max_watch_seconds_per_day'] ?? '')" />
    </div>
</div>

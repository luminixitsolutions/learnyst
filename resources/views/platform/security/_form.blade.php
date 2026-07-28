<form method="POST" action="{{ $action }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
    @csrf
    @method('PUT')
    @if(!empty($section))
        <input type="hidden" name="section" value="{{ $section }}">
    @endif

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Soft maintenance flag and IP allowlist are stored for platform control. This does <strong>not</strong> run Laravel <code>artisan down</code>, so institute panel impersonation stays available.
    </div>

    <label class="flex items-center gap-3">
        <input type="hidden" name="maintenance_mode" value="0">
        <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $security['maintenance_mode']) === '1') class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Enable maintenance mode flag</span>
    </label>

    <div class="space-y-1.5">
        <label class="block text-sm font-medium text-slate-700">Maintenance message</label>
        <textarea name="maintenance_message" rows="3" class="panel-input w-full">{{ old('maintenance_message', $security['maintenance_message']) }}</textarea>
    </div>

    <label class="flex items-center gap-3">
        <input type="hidden" name="ip_allowlist_enabled" value="0">
        <input type="checkbox" name="ip_allowlist_enabled" value="1" @checked(old('ip_allowlist_enabled', $security['ip_allowlist_enabled']) === '1') class="rounded border-slate-300 text-brand-600">
        <span class="text-sm text-slate-700">Enable IP allowlist flag</span>
    </label>

    <div class="space-y-1.5">
        <label class="block text-sm font-medium text-slate-700">Allowed IPs</label>
        <textarea name="ip_allowlist" rows="4" placeholder="One IP or CIDR per line" class="panel-input w-full font-mono text-xs">{{ old('ip_allowlist', $security['ip_allowlist']) }}</textarea>
        <p class="text-xs text-slate-500">Stored for future enforcement. Enforcement is not active yet to avoid locking out admins.</p>
    </div>

    <button type="submit" class="panel-btn-primary">Save Security Settings</button>
</form>

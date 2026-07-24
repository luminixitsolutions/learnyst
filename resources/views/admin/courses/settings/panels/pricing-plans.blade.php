@php
    $statusFilter = $statusFilter ?? request('status', 'all');
    $search = $search ?? request('search');
    $tabs = [
        'all' => 'ALL',
        'draft' => 'DRAFT',
        'published' => 'PUBLISHED',
        'unpublished' => 'UNPUBLISHED',
        'archived' => 'ARCHIVED',
    ];
    $filtered = $plans->when($statusFilter !== 'all', fn ($c) => $c->where('status', $statusFilter))
        ->when($search, fn ($c) => $c->filter(fn ($p) => str_contains(strtolower($p->title), strtolower($search))));
@endphp

<div x-data="{ drawer: false }">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <button type="button" @click="drawer = true"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Pricing Plans
        </button>
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.courses.settings.show', [$course, $panel]) }}?status={{ $key }}{{ $search ? '&search='.urlencode($search) : '' }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide transition
               {{ $statusFilter === $key ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.courses.settings.show', [$course, $panel]) }}" class="mt-4">
        <input type="hidden" name="status" value="{{ $statusFilter }}">
        <div class="relative max-w-sm">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
            <input type="search" name="search" value="{{ $search }}" placeholder="Search by title"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
        </div>
    </form>

    <div class="mt-6">
        @if($filtered->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 p-12 text-center">
                <p class="text-sm font-medium text-slate-700">No results found</p>
                <p class="text-sm text-slate-500 mt-1">Try another filter or create a new pricing plan.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($filtered as $plan)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $plan->title }}</p>
                                    @if($plan->description)
                                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $plan->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ str_replace('_', ' ', $plan->plan_type) }}</td>
                                <td class="px-4 py-3 text-slate-800">
                                    @if($plan->plan_type === 'free')
                                        Free
                                    @else
                                        {{ $plan->currency ?? 'INR' }}
                                        {{ number_format((float) ($plan->offer_price ?? $plan->regular_price ?? 0), 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $plan->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $plan->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1 flex-wrap">
                                        @if($plan->status !== 'published')
                                            <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.status', [$course, $plan]) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="published">
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50 rounded-lg">Publish</button>
                                            </form>
                                        @endif
                                        @if($plan->status === 'published')
                                            <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.status', [$course, $plan]) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="unpublished">
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Unpublish</button>
                                            </form>
                                        @endif
                                        @if($plan->status !== 'archived')
                                            <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.status', [$course, $plan]) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="archived">
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-50 rounded-lg">Archive</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.duplicate', [$course, $plan]) }}">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Duplicate</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.destroy', [$course, $plan]) }}"
                                              onsubmit="return confirm('Delete this pricing plan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded-lg">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Create drawer --}}
    <div x-show="drawer" x-cloak class="fixed inset-0 z-50 flex justify-end">
        <div class="absolute inset-0 bg-black/40" @click="drawer = false"></div>
        <div class="relative w-full max-w-lg h-full bg-white shadow-xl overflow-y-auto" @click.stop>
            <div class="sticky top-0 bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Add pricing plan</h2>
                <button type="button" @click="drawer = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.courses.settings.pricing-plans.store', $course) }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                    @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Plan type <span class="text-rose-500">*</span></label>
                    <select name="plan_type" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                        @foreach(['free' => 'Free', 'one_time' => 'One time', 'limited_offer' => 'Limited offer', 'subscription' => 'Subscription', 'installment' => 'Installment', 'custom' => 'Custom'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('plan_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('plan_type')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Regular price</label>
                        <input type="number" step="0.01" min="0" name="regular_price" value="{{ old('regular_price') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('regular_price')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Offer price</label>
                        <input type="number" step="0.01" min="0" name="offer_price" value="{{ old('offer_price') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('offer_price')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', 'INR') }}" maxlength="10"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Validity (days)</label>
                        <input type="number" name="validity_days" min="1" value="{{ old('validity_days') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Trial days</label>
                        <input type="number" name="trial_days" min="0" value="{{ old('trial_days') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Enrollment limit</label>
                        <input type="number" name="enrollment_limit" min="1" value="{{ old('enrollment_limit') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase starts</label>
                        <input type="datetime-local" name="purchase_starts_at" value="{{ old('purchase_starts_at') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase ends</label>
                        <input type="datetime-local" name="purchase_ends_at" value="{{ old('purchase_ends_at') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Offer starts</label>
                        <input type="datetime-local" name="offer_starts_at" value="{{ old('offer_starts_at') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Offer ends</label>
                        <input type="datetime-local" name="offer_ends_at" value="{{ old('offer_ends_at') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Billing frequency</label>
                        <input type="text" name="billing_frequency" value="{{ old('billing_frequency') }}" placeholder="e.g. monthly"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Setup fee</label>
                        <input type="number" step="0.01" min="0" name="setup_fee" value="{{ old('setup_fee') }}"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    @foreach([
                        'lifetime_access' => 'Lifetime access',
                        'is_public' => 'Public plan',
                        'coupon_eligible' => 'Coupon eligible',
                        'auto_renew' => 'Auto renew',
                        'show_countdown' => 'Show countdown',
                    ] as $field => $label)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                   @checked(old($field, in_array($field, ['is_public', 'coupon_eligible', 'auto_renew'], true)))>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="drawer = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Create plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

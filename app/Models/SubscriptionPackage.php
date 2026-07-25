<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'price_monthly',
        'price_yearly',
        'currency',
        'is_free',
        'is_custom',
        'trial_days',
        'features',
        'cta_label',
        'cta_url',
        'badge',
        'is_featured',
        'is_active',
        'sort_order',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'is_free' => 'boolean',
            'is_custom' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'trial_days' => 'integer',
            'sort_order' => 'integer',
            'features' => 'array',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $package) {
            if (blank($package->slug) && filled($package->name)) {
                $package->slug = static::uniqueSlug($package->name, $package->id);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'package';
        $slug = $base;
        $i = 2;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function displayPrice(string $billing = 'monthly'): ?float
    {
        if ($this->is_free) {
            return 0.0;
        }

        if ($this->is_custom) {
            return null;
        }

        return $billing === 'yearly'
            ? ($this->price_yearly !== null ? (float) $this->price_yearly : null)
            : ($this->price_monthly !== null ? (float) $this->price_monthly : null);
    }

    public function formattedPrice(string $billing = 'monthly'): string
    {
        if ($this->is_free) {
            return 'Free';
        }

        if ($this->is_custom) {
            return 'Custom';
        }

        $price = $this->displayPrice($billing);
        if ($price === null) {
            return '—';
        }

        $symbol = $this->currencySymbol();

        return $symbol.number_format($price, $price == floor($price) ? 0 : 2);
    }

    public function currencySymbol(): string
    {
        return match (strtoupper($this->currency ?: 'INR')) {
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => strtoupper($this->currency).' ',
        };
    }

    public function resolvedCtaUrl(): string
    {
        if (filled($this->cta_url)) {
            return $this->cta_url;
        }

        return $this->is_custom
            ? url(config('website.cta.demo', '/product-demo'))
            : url(config('website.cta.trial', '/signup'));
    }

    public function featureList(): array
    {
        return array_values(array_filter(
            array_map(
                fn ($f) => is_string($f) ? trim($f) : trim((string) ($f['text'] ?? $f['label'] ?? '')),
                $this->features ?? []
            ),
            fn ($f) => $f !== ''
        ));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffiliateLink extends Model
{
    protected $fillable = [
        'affiliate_id',
        'created_by',
        'product_type',
        'product_id',
        'slug',
        'url_path',
        'clicks',
        'conversions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'clicks' => 'integer',
            'conversions' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function conversionRate(): float
    {
        if ($this->clicks <= 0) {
            return 0;
        }

        return round(($this->conversions / $this->clicks) * 100, 1);
    }

    public static function generateSlug(Affiliate $affiliate, string $productType, ?int $productId = null): string
    {
        $base = Str::slug($affiliate->code.'-'.$productType.($productId ? '-'.$productId : ''));
        $slug = $base;
        $i = 1;
        while (static::where('affiliate_id', $affiliate->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

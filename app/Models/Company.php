<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'owner_user_id',
        'subscription_package_id',
        'package_assigned_at',
        'name',
        'slug',
        'tagline',
        'about',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'theme_tokens',
        'email_from_name',
        'email_from_address',
        'cover_image',
        'email',
        'phone',
        'website_url',
        'custom_domain',
        'domain_verification_token',
        'domain_verified_at',
        'address',
        'city',
        'social_links',
        'highlights',
        'profile',
        'is_public',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'highlights' => 'array',
            'profile' => 'array',
            'theme_tokens' => 'array',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'domain_verified_at' => 'datetime',
            'package_assigned_at' => 'datetime',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'created_by', 'owner_user_id');
    }

    public function publishedCourses()
    {
        return $this->courses()->published();
    }

    public function testimonials()
    {
        return $this->hasMany(CompanyTestimonial::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function reviews()
    {
        return $this->hasMany(CompanyReview::class)->latest();
    }

    public function enquiries()
    {
        return $this->hasMany(CompanyEnquiry::class)->latest();
    }

    public function videos()
    {
        return $this->hasMany(CompanyVideo::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function blogs()
    {
        return $this->hasMany(CompanyBlog::class)->latest('published_at')->latest('id');
    }

    public function galleryItems()
    {
        return $this->hasMany(CompanyGalleryItem::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function teamMembers()
    {
        return $this->hasMany(CompanyTeamMember::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function scopePublicListed($query)
    {
        return $query->where('is_public', true)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function logoUrl(): string
    {
        return self::mediaUrl($this->logo);
    }

    public function coverUrl(): string
    {
        return self::mediaUrl($this->cover_image);
    }

    public function profileValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->profile ?? [], $key, $default);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = collect($parts)->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');

        return $letters !== '' ? $letters : 'CO';
    }

    public static function mediaUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');

        // Public website assets (tracked in git)
        if (str_starts_with($path, 'website/')) {
            return '/'.$path;
        }

        // Uploaded media on the public disk
        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'company';
        $slug = $base;
        $i = 1;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public static function firstOrCreateForOwner(User $owner): self
    {
        $existing = static::query()->where('owner_user_id', $owner->id)->first();
        if ($existing) {
            return $existing;
        }

        return static::create([
            'owner_user_id' => $owner->id,
            'name' => $owner->name ?: 'My Academy',
            'slug' => static::uniqueSlug($owner->name ?: 'academy-'.$owner->id),
            'email' => $owner->email,
            'phone' => $owner->phone,
            'address' => $owner->address,
            'about' => $owner->bio,
            'logo' => $owner->avatar,
            'is_public' => true,
            'is_active' => true,
            'highlights' => [
                'Branded online academy',
                'Secure course delivery',
                'Learner-first experience',
            ],
            'social_links' => $owner->social_links ?? [],
        ]);
    }
}

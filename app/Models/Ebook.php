<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ebook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'cover_path',
        'price',
        'is_free',
        'content_security',
        'status',
        'allow_download',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'allow_download' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ebook $ebook) {
            if (empty($ebook->slug)) {
                $ebook->slug = Str::slug(Str::limit($ebook->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contentSecurityLabel(): string
    {
        return match ($this->content_security) {
            'no_encryption' => 'No Encryption',
            default => 'Encryption',
        };
    }
}

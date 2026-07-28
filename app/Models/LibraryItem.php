<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryItem extends Model
{
    protected $fillable = [
        'created_by', 'title', 'slug', 'item_type', 'description', 'author',
        'cover_path', 'file_path', 'external_url', 'allow_download', 'access_mode',
        'course_id', 'ebook_id', 'resource_id', 'status', 'view_count', 'download_count',
    ];

    protected function casts(): array
    {
        return ['allow_download' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug(Str::limit($item->title, 40, '')).'-'.Str::lower(Str::random(4));
            }
        });
    }

    public static function types(): array
    {
        return [
            'ebook' => 'eBook',
            'journal' => 'Journal',
            'previous_paper' => 'Previous paper',
            'research' => 'Research material',
            'resource' => 'Resource',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function fileUrl(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : $this->external_url;
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }
}

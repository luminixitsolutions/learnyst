<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class CompanyService
{
    public static function syncMissingCompanies(): int
    {
        $adminRoleId = Role::query()->where('slug', 'admin')->value('id');
        if (! $adminRoleId) {
            return 0;
        }

        $created = 0;
        User::query()
            ->where('role_id', $adminRoleId)
            ->where('is_active', true)
            ->orderBy('id')
            ->each(function (User $user) use (&$created) {
                $before = Company::query()->where('owner_user_id', $user->id)->exists();
                Company::firstOrCreateForOwner($user);
                if (! $before) {
                    $created++;
                }
            });

        return $created;
    }

    public static function publicCompanies(?string $search = null)
    {
        self::syncMissingCompanies();

        return Company::query()
            ->publicListed()
            ->withCount(['publishedCourses as courses_count'])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
            ->withAvg(['reviews as institute_avg_rating' => fn ($q) => $q->approved()], 'rating')
            ->selectSub(function ($query) {
                $query->from('course_reviews')
                    ->join('courses', 'courses.id', '=', 'course_reviews.course_id')
                    ->whereColumn('courses.created_by', 'companies.owner_user_id')
                    ->where('course_reviews.status', 'approved')
                    ->whereNull('course_reviews.deleted_at')
                    ->selectRaw('AVG(course_reviews.rating)');
            }, 'course_avg_rating')
            ->selectSub(function ($query) {
                $query->from('course_reviews')
                    ->join('courses', 'courses.id', '=', 'course_reviews.course_id')
                    ->whereColumn('courses.created_by', 'companies.owner_user_id')
                    ->where('course_reviews.status', 'approved')
                    ->whereNull('course_reviews.deleted_at')
                    ->selectRaw('COUNT(*)');
            }, 'course_reviews_count')
            ->with('owner')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('tagline', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            // Featured institute always first, then highest rated.
            ->orderByRaw("CASE WHEN companies.slug = 'luminix-it-solution' THEN 0 ELSE 1 END")
            ->orderByRaw('COALESCE(institute_avg_rating, 0) DESC')
            ->orderByDesc('reviews_count')
            ->orderByDesc('courses_count')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
    }

    public static function findPublicBySlug(string $slug): ?Company
    {
        self::syncMissingCompanies();

        return Company::query()
            ->publicListed()
            ->where('slug', $slug)
            ->with('owner')
            ->first();
    }

    public static function findBySlug(string $slug): ?Company
    {
        self::syncMissingCompanies();

        return Company::query()
            ->where('slug', $slug)
            ->with('owner')
            ->first();
    }

    public static function resolveForUser(User $user): Company
    {
        if ($user->isAdmin()) {
            return Company::firstOrCreateForOwner($user);
        }

        if ($user->isSubAdmin() && $user->created_by) {
            $owner = User::query()->find($user->created_by);
            if ($owner) {
                return Company::firstOrCreateForOwner($owner);
            }
        }

        abort(403, 'Only company admins can manage the company profile.');
    }

    public static function normalizeHighlights(array|string|null $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return collect($value ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->take(8)
            ->values()
            ->all();
    }

    public static function normalizeSocialLinks(array $links): array
    {
        $allowed = ['website', 'facebook', 'instagram', 'youtube', 'linkedin', 'twitter', 'telegram'];
        $normalized = [];

        foreach ($allowed as $key) {
            $val = trim((string) ($links[$key] ?? ''));
            if ($val !== '') {
                $normalized[$key] = $val;
            }
        }

        return $normalized;
    }

    public static function normalizeLines(array|string|null $value, int $limit = 12): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return collect($value ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->take($limit)
            ->values()
            ->all();
    }

    public static function normalizeProfile(array $input, ?array $existing = null): array
    {
        $existing = $existing ?? [];

        $stats = [];
        foreach (($input['stats'] ?? []) as $stat) {
            $label = trim((string) ($stat['label'] ?? ''));
            $value = trim((string) ($stat['value'] ?? ''));
            if ($label !== '' && $value !== '') {
                $stats[] = ['label' => $label, 'value' => $value];
            }
        }

        $whyUs = [];
        foreach (($input['why_us'] ?? []) as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            $text = trim((string) ($item['text'] ?? ''));
            if ($title !== '' || $text !== '') {
                $whyUs[] = [
                    'title' => $title,
                    'text' => $text,
                    'icon' => trim((string) ($item['icon'] ?? 'fa-check-circle')) ?: 'fa-check-circle',
                ];
            }
        }

        $faqs = [];
        foreach (($input['faqs'] ?? []) as $faq) {
            $q = trim((string) ($faq['q'] ?? ''));
            $a = trim((string) ($faq['a'] ?? ''));
            if ($q !== '' && $a !== '') {
                $faqs[] = ['q' => $q, 'a' => $a];
            }
        }

        $team = [];
        foreach (($input['team'] ?? []) as $member) {
            $name = trim((string) ($member['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $team[] = [
                'name' => $name,
                'role' => trim((string) ($member['role'] ?? '')),
                'bio' => trim((string) ($member['bio'] ?? '')),
                'photo' => trim((string) ($member['photo'] ?? ($member['existing_photo'] ?? ''))),
            ];
        }

        $gallery = $existing['gallery'] ?? [];
        if (array_key_exists('gallery_keep', $input)) {
            $keep = collect($input['gallery_keep'] ?? [])->map(fn ($p) => (string) $p)->filter()->all();
            $gallery = collect($gallery)
                ->filter(function ($item) use ($keep) {
                    $path = is_string($item) ? $item : ($item['path'] ?? '');

                    return in_array($path, $keep, true);
                })
                ->values()
                ->all();
        }

        return [
            'mission' => trim((string) ($input['mission'] ?? '')),
            'vision' => trim((string) ($input['vision'] ?? '')),
            'founded_year' => trim((string) ($input['founded_year'] ?? '')),
            'state' => trim((string) ($input['state'] ?? '')),
            'country' => trim((string) ($input['country'] ?? 'India')),
            'working_hours' => trim((string) ($input['working_hours'] ?? '')),
            'specialties' => self::normalizeLines($input['specialties'] ?? '', 16),
            'stats' => array_slice($stats, 0, 6),
            'why_us' => array_slice($whyUs, 0, 6),
            'faqs' => array_slice($faqs, 0, 8),
            'team' => array_slice($team, 0, 8),
            'gallery' => $gallery,
        ];
    }
}

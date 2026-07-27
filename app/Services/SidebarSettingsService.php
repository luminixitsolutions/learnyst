<?php

namespace App\Services;

use App\Models\Company;

class SidebarSettingsService
{
    public const THEMES = [
        'indigo' => [
            'label' => 'Indigo Violet',
            'preview' => 'linear-gradient(135deg, #c7d2fe, #ddd6fe, #bfdbfe)',
            'accent' => '#6366f1',
            'accent_dark' => '#4338ca',
            'bg' => 'linear-gradient(168deg, #dbeafe 0%, #e0e7ff 22%, #ddd6fe 48%, #ede9fe 72%, #e0f2fe 100%)',
            'mesh' => 'radial-gradient(ellipse 90% 55% at 0% 0%, rgba(99, 102, 241, 0.28) 0%, transparent 55%), radial-gradient(ellipse 70% 45% at 100% 85%, rgba(139, 92, 246, 0.22) 0%, transparent 50%), radial-gradient(ellipse 50% 35% at 50% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 60%)',
            'accent_gradient' => 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 55%, #a855f7 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(99, 102, 241, 0.22) 0%, rgba(139, 92, 246, 0.14) 60%, rgba(59, 130, 246, 0.1) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, rgba(238, 242, 255, 0.45) 100%)',
            'glow1' => 'rgba(99, 102, 241, 0.38)',
            'glow2' => 'rgba(139, 92, 246, 0.3)',
            'border' => 'rgba(129, 140, 248, 0.45)',
            'stripe' => 'linear-gradient(180deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%)',
        ],
        'violet' => [
            'label' => 'Royal Violet',
            'preview' => 'linear-gradient(135deg, #ddd6fe, #e9d5ff, #f5d0fe)',
            'accent' => '#7c3aed',
            'accent_dark' => '#6d28d9',
            'bg' => 'linear-gradient(168deg, #ede9fe 0%, #e9d5ff 25%, #f3e8ff 50%, #faf5ff 75%, #fdf4ff 100%)',
            'mesh' => 'radial-gradient(ellipse 85% 50% at 5% 10%, rgba(124, 58, 237, 0.3) 0%, transparent 55%), radial-gradient(ellipse 65% 40% at 95% 90%, rgba(192, 132, 252, 0.25) 0%, transparent 50%)',
            'accent_gradient' => 'linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #d946ef 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(124, 58, 237, 0.22) 0%, rgba(168, 85, 247, 0.14) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(245, 243, 255, 0.45) 100%)',
            'glow1' => 'rgba(124, 58, 237, 0.35)',
            'glow2' => 'rgba(192, 132, 252, 0.28)',
            'border' => 'rgba(192, 132, 252, 0.45)',
            'stripe' => 'linear-gradient(180deg, #7c3aed 0%, #a855f7 50%, #d946ef 100%)',
        ],
        'emerald' => [
            'label' => 'Emerald Teal',
            'preview' => 'linear-gradient(135deg, #a7f3d0, #99f6e4, #a5f3fc)',
            'accent' => '#059669',
            'accent_dark' => '#047857',
            'bg' => 'linear-gradient(168deg, #d1fae5 0%, #ccfbf1 30%, #ecfdf5 55%, #ecfeff 80%, #cffafe 100%)',
            'mesh' => 'radial-gradient(ellipse 85% 50% at 0% 0%, rgba(5, 150, 105, 0.25) 0%, transparent 55%), radial-gradient(ellipse 65% 40% at 100% 90%, rgba(20, 184, 166, 0.22) 0%, transparent 50%)',
            'accent_gradient' => 'linear-gradient(135deg, #059669 0%, #0d9488 50%, #0891b2 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(5, 150, 105, 0.2) 0%, rgba(20, 184, 166, 0.12) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, rgba(236, 253, 245, 0.45) 100%)',
            'glow1' => 'rgba(16, 185, 129, 0.32)',
            'glow2' => 'rgba(45, 212, 191, 0.26)',
            'border' => 'rgba(110, 231, 183, 0.45)',
            'stripe' => 'linear-gradient(180deg, #059669 0%, #0d9488 50%, #0891b2 100%)',
        ],
        'rose' => [
            'label' => 'Rose Pink',
            'preview' => 'linear-gradient(135deg, #fecdd3, #fbcfe8, #fed7aa)',
            'accent' => '#e11d48',
            'accent_dark' => '#be123c',
            'bg' => 'linear-gradient(168deg, #ffe4e6 0%, #fce7f3 28%, #fff1f2 52%, #ffedd5 78%, #fef2f2 100%)',
            'mesh' => 'radial-gradient(ellipse 85% 50% at 0% 5%, rgba(225, 29, 72, 0.22) 0%, transparent 55%), radial-gradient(ellipse 65% 40% at 100% 88%, rgba(244, 114, 182, 0.2) 0%, transparent 50%)',
            'accent_gradient' => 'linear-gradient(135deg, #e11d48 0%, #ec4899 50%, #f97316 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(225, 29, 72, 0.18) 0%, rgba(244, 114, 182, 0.12) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, rgba(255, 241, 242, 0.45) 100%)',
            'glow1' => 'rgba(225, 29, 72, 0.28)',
            'glow2' => 'rgba(244, 114, 182, 0.24)',
            'border' => 'rgba(253, 164, 175, 0.5)',
            'stripe' => 'linear-gradient(180deg, #e11d48 0%, #ec4899 50%, #f97316 100%)',
        ],
        'slate' => [
            'label' => 'Slate Blue',
            'preview' => 'linear-gradient(135deg, #cbd5e1, #e2e8f0, #bfdbfe)',
            'accent' => '#475569',
            'accent_dark' => '#334155',
            'bg' => 'linear-gradient(168deg, #e2e8f0 0%, #f1f5f9 35%, #e2e8f0 65%, #dbeafe 100%)',
            'mesh' => 'radial-gradient(ellipse 80% 45% at 10% 10%, rgba(71, 85, 105, 0.18) 0%, transparent 55%), radial-gradient(ellipse 60% 35% at 90% 85%, rgba(100, 116, 139, 0.14) 0%, transparent 50%)',
            'accent_gradient' => 'linear-gradient(135deg, #475569 0%, #64748b 50%, #3b82f6 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(71, 85, 105, 0.16) 0%, rgba(100, 116, 139, 0.1) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, rgba(248, 250, 252, 0.45) 100%)',
            'glow1' => 'rgba(100, 116, 139, 0.22)',
            'glow2' => 'rgba(148, 163, 184, 0.18)',
            'border' => 'rgba(148, 163, 184, 0.5)',
            'stripe' => 'linear-gradient(180deg, #475569 0%, #64748b 50%, #3b82f6 100%)',
        ],
        'ocean' => [
            'label' => 'Ocean Blue',
            'preview' => 'linear-gradient(135deg, #93c5fd, #7dd3fc, #67e8f9)',
            'accent' => '#0284c7',
            'accent_dark' => '#0369a1',
            'bg' => 'linear-gradient(168deg, #bfdbfe 0%, #bae6fd 25%, #e0f2fe 50%, #ecfeff 75%, #cffafe 100%)',
            'mesh' => 'radial-gradient(ellipse 85% 50% at 0% 0%, rgba(2, 132, 199, 0.28) 0%, transparent 55%), radial-gradient(ellipse 65% 40% at 100% 90%, rgba(34, 211, 238, 0.22) 0%, transparent 50%)',
            'accent_gradient' => 'linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #06b6d4 100%)',
            'active_bg' => 'linear-gradient(90deg, rgba(2, 132, 199, 0.2) 0%, rgba(14, 165, 233, 0.12) 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, rgba(239, 246, 255, 0.45) 100%)',
            'glow1' => 'rgba(14, 165, 233, 0.32)',
            'glow2' => 'rgba(34, 211, 238, 0.26)',
            'border' => 'rgba(125, 211, 252, 0.5)',
            'stripe' => 'linear-gradient(180deg, #0284c7 0%, #0ea5e9 50%, #06b6d4 100%)',
        ],
    ];

    public static function defaults(): array
    {
        return [
            'layout' => 'vertical',
            'theme' => 'indigo',
            'menu_order' => [],
            'custom_colors' => self::defaultCustomColors(),
        ];
    }

    public static function defaultCustomColors(): array
    {
        return [
            'primary' => '#6366f1',
            'secondary' => '#8b5cf6',
            'bg_start' => '#dbeafe',
            'bg_end' => '#ede9fe',
        ];
    }

    public static function allowedThemeKeys(): array
    {
        return array_merge(array_keys(self::THEMES), ['custom']);
    }

    public static function forCompany(?Company $company): array
    {
        $defaults = self::defaults();
        if (! $company) {
            return $defaults;
        }

        $stored = data_get($company->profile, 'sidebar', []);

        return [
            'layout' => in_array($stored['layout'] ?? '', ['vertical', 'horizontal'], true)
                ? $stored['layout']
                : $defaults['layout'],
            'theme' => in_array($stored['theme'] ?? '', self::allowedThemeKeys(), true)
                ? $stored['theme']
                : $defaults['theme'],
            'menu_order' => is_array($stored['menu_order'] ?? null) ? $stored['menu_order'] : [],
            'custom_colors' => self::normalizeCustomColors($stored['custom_colors'] ?? null),
        ];
    }

    public static function normalizeCustomColors(?array $colors): array
    {
        $defaults = self::defaultCustomColors();

        return [
            'primary' => self::normalizeHex($colors['primary'] ?? null, $defaults['primary']),
            'secondary' => self::normalizeHex($colors['secondary'] ?? null, $defaults['secondary']),
            'bg_start' => self::normalizeHex($colors['bg_start'] ?? null, $defaults['bg_start']),
            'bg_end' => self::normalizeHex($colors['bg_end'] ?? null, $defaults['bg_end']),
        ];
    }

    public static function normalizeHex(?string $value, string $fallback): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $value = ltrim(trim($value), '#');
        if (! preg_match('/^[0-9a-fA-F]{6}$/', $value)) {
            return $fallback;
        }

        return '#'.strtolower($value);
    }

    public static function save(Company $company, array $input): void
    {
        $profile = $company->profile ?? [];
        $existingSidebar = $profile['sidebar'] ?? [];
        $menuOrder = collect($input['menu_order'] ?? [])
            ->filter(fn ($key) => is_string($key) && $key !== '')
            ->values()
            ->all();

        $profile['sidebar'] = [
            'layout' => $input['layout'] ?? 'vertical',
            'theme' => in_array($input['theme'] ?? '', self::allowedThemeKeys(), true) ? $input['theme'] : 'indigo',
            'menu_order' => $menuOrder,
            'custom_colors' => self::normalizeCustomColors([
                'primary' => $input['custom_primary'] ?? data_get($existingSidebar, 'custom_colors.primary'),
                'secondary' => $input['custom_secondary'] ?? data_get($existingSidebar, 'custom_colors.secondary'),
                'bg_start' => $input['custom_bg_start'] ?? data_get($existingSidebar, 'custom_colors.bg_start'),
                'bg_end' => $input['custom_bg_end'] ?? data_get($existingSidebar, 'custom_colors.bg_end'),
            ]),
        ];

        $company->update(['profile' => $profile]);
    }

    public static function sectionKey(array $section): string
    {
        if (isset($section['group'])) {
            return 'group:'.$section['group'];
        }

        return 'label:'.($section['label'] ?? 'unknown');
    }

    public static function reorderSections(array $sections, array $order): array
    {
        if (empty($order)) {
            return $sections;
        }

        $map = collect($sections)->keyBy(fn ($section) => self::sectionKey($section));
        $ordered = [];

        foreach ($order as $key) {
            if ($map->has($key)) {
                $ordered[] = $map->get($key);
                $map->forget($key);
            }
        }

        foreach ($map as $remaining) {
            $ordered[] = $remaining;
        }

        return $ordered;
    }

    public static function menuOrderLabels(array $sections): array
    {
        return collect($sections)->map(function ($section) {
            return [
                'key' => self::sectionKey($section),
                'label' => $section['group'] ?? $section['label'] ?? 'Menu',
                'type' => isset($section['group']) ? 'group' : 'link',
            ];
        })->values()->all();
    }

    public static function theme(string $key, ?array $customColors = null): array
    {
        if ($key === 'custom') {
            return self::buildCustomTheme($customColors ?? self::defaultCustomColors());
        }

        return self::THEMES[$key] ?? self::THEMES['indigo'];
    }

    public static function resolveTheme(array $settings): array
    {
        $key = $settings['theme'] ?? 'indigo';

        if ($key === 'custom') {
            return self::buildCustomTheme($settings['custom_colors'] ?? self::defaultCustomColors());
        }

        return self::theme($key);
    }

    public static function buildCustomTheme(array $colors): array
    {
        $colors = self::normalizeCustomColors($colors);
        $primary = $colors['primary'];
        $secondary = $colors['secondary'];
        $bgStart = $colors['bg_start'];
        $bgEnd = $colors['bg_end'];

        return [
            'label' => 'Custom Theme',
            'preview' => "linear-gradient(135deg, {$bgStart}, {$secondary}, {$bgEnd})",
            'accent' => $primary,
            'accent_dark' => self::darkenHex($primary, 14),
            'bg' => "linear-gradient(168deg, {$bgStart} 0%, {$bgEnd} 55%, {$bgStart} 100%)",
            'mesh' => 'radial-gradient(ellipse 90% 55% at 0% 0%, '.self::rgba($primary, 0.28).' 0%, transparent 55%), radial-gradient(ellipse 70% 45% at 100% 85%, '.self::rgba($secondary, 0.22).' 0%, transparent 50%)',
            'accent_gradient' => "linear-gradient(135deg, {$primary} 0%, {$secondary} 100%)",
            'active_bg' => 'linear-gradient(90deg, '.self::rgba($primary, 0.22).' 0%, '.self::rgba($secondary, 0.14).' 100%)',
            'hover_bg' => 'linear-gradient(90deg, rgba(255, 255, 255, 0.55) 0%, '.self::rgba($bgStart, 0.45).' 100%)',
            'glow1' => self::rgba($primary, 0.35),
            'glow2' => self::rgba($secondary, 0.28),
            'border' => self::rgba($primary, 0.4),
            'stripe' => "linear-gradient(180deg, {$primary} 0%, {$secondary} 100%)",
        ];
    }

    protected static function hexToRgb(string $hex): array
    {
        $hex = ltrim(self::normalizeHex($hex, '#000000'), '#');

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    protected static function rgba(string $hex, float $alpha): string
    {
        $rgb = self::hexToRgb($hex);

        return sprintf('rgba(%d, %d, %d, %.2f)', $rgb['r'], $rgb['g'], $rgb['b'], $alpha);
    }

    protected static function darkenHex(string $hex, int $percent): string
    {
        $rgb = self::hexToRgb($hex);
        $factor = max(0, 1 - ($percent / 100));

        return sprintf(
            '#%02x%02x%02x',
            (int) round($rgb['r'] * $factor),
            (int) round($rgb['g'] * $factor),
            (int) round($rgb['b'] * $factor)
        );
    }

    public static function cssVariables(array $settings): string
    {
        $theme = self::resolveTheme($settings);

        return implode("\n", [
            '--panel-sidebar-bg: '.$theme['bg'].';',
            '--panel-sidebar-mesh: '.$theme['mesh'].';',
            '--panel-sidebar-accent: '.$theme['accent'].';',
            '--panel-sidebar-accent-dark: '.$theme['accent_dark'].';',
            '--panel-sidebar-accent-gradient: '.$theme['accent_gradient'].';',
            '--panel-sidebar-active-bg: '.$theme['active_bg'].';',
            '--panel-sidebar-hover-bg: '.$theme['hover_bg'].';',
            '--panel-sidebar-border: '.$theme['border'].';',
            '--panel-sidebar-glow1: '.$theme['glow1'].';',
            '--panel-sidebar-glow2: '.$theme['glow2'].';',
            '--panel-sidebar-stripe: '.$theme['stripe'].';',
        ]);
    }
}

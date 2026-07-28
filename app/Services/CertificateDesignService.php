<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CertificateDesignService
{
    public const PLACEHOLDERS = [
        'student_name' => 'Learner full name',
        'course_name' => 'Course title',
        'cert_id' => 'Certificate number',
        'verify_url' => 'Public verify URL',
        'issue_date' => 'Issue date',
        'institute_name' => 'Institute / brand name',
    ];

    public const ELEMENT_KEYS = [
        'title',
        'subtitle',
        'student',
        'body',
        'course',
        'left_sign',
        'right_sign',
        'footer',
    ];

    public const ELEMENT_LABELS = [
        'title' => 'Title',
        'subtitle' => 'Subtitle',
        'student' => 'Student name',
        'body' => 'Body text',
        'course' => 'Course name',
        'left_sign' => 'Left signatory',
        'right_sign' => 'Right signatory',
        'footer' => 'Footer',
    ];

    public function defaultLayout(): array
    {
        return array_merge($this->presetLayout('classic-blue-gold'), [
            'element_positions' => $this->defaultElementPositions(),
        ]);
    }

    public function defaultElementPositions(): array
    {
        return collect(self::ELEMENT_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => ['x' => 0, 'y' => 0]])
            ->all();
    }

    public function sanitizeElementPositions(array $input): array
    {
        $positions = $this->defaultElementPositions();

        foreach (self::ELEMENT_KEYS as $key) {
            if (! isset($input[$key]) || ! is_array($input[$key])) {
                continue;
            }

            $positions[$key] = [
                'x' => max(-280, min(280, (int) ($input[$key]['x'] ?? 0))),
                'y' => max(-280, min(280, (int) ($input[$key]['y'] ?? 0))),
            ];
        }

        return $positions;
    }

    public function positionStyle(array $layout, string $key): string
    {
        $positions = $this->sanitizeElementPositions($layout['element_positions'] ?? []);
        $pos = $positions[$key] ?? ['x' => 0, 'y' => 0];
        $x = (int) ($pos['x'] ?? 0);
        $y = (int) ($pos['y'] ?? 0);

        if ($x === 0 && $y === 0) {
            return '';
        }

        return 'transform: translate('.$x.'px, '.$y.'px)';
    }

    public function positionAttribute(array $layout, string $key): string
    {
        $style = $this->positionStyle($layout, $key);

        return $style !== '' ? ' style="'.$style.'"' : '';
    }

    /**
     * @return array<string, array{key: string, name: string, description: string, layout: array<string, mixed>}>
     */
    public function presets(): array
    {
        $items = [
            'classic-blue-gold' => [
                'name' => 'Classic Blue & Gold',
                'description' => 'Traditional formal certificate with navy border and gold trim.',
            ],
            'emerald-elegance' => [
                'name' => 'Emerald Elegance',
                'description' => 'Fresh green tones for professional achievement awards.',
            ],
            'minimal-slate' => [
                'name' => 'Minimal Slate',
                'description' => 'Clean, modern layout with subtle grey accents.',
            ],
            'royal-navy' => [
                'name' => 'Royal Navy',
                'description' => 'Deep navy frame with silver accent details.',
            ],
            'teal-modern' => [
                'name' => 'Teal Modern',
                'description' => 'Contemporary teal design aligned with your brand.',
            ],
            'burgundy-heritage' => [
                'name' => 'Burgundy Heritage',
                'description' => 'Rich burgundy and cream for academic excellence.',
            ],
            'forest-academy' => [
                'name' => 'Forest Academy',
                'description' => 'Earthy greens suited for training programs.',
            ],
            'indigo-executive' => [
                'name' => 'Indigo Executive',
                'description' => 'Bold indigo layout for corporate certifications.',
            ],
        ];

        $presets = [];
        foreach ($items as $key => $meta) {
            $presets[$key] = [
                'key' => $key,
                'name' => $meta['name'],
                'description' => $meta['description'],
                'layout' => $this->presetLayout($key),
            ];
        }

        return $presets;
    }

    public function presetKeys(): array
    {
        return array_keys($this->presets());
    }

    public function presetLayout(string $key): array
    {
        $layouts = [
            'classic-blue-gold' => [
                'preset_key' => 'classic-blue-gold',
                'theme' => 'classic-blue-gold',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Completion',
                'subtitle' => 'This Certificate is Proudly Presented to',
                'body' => 'for successfully completing all requirements of the course and demonstrating the required knowledge and skills.',
                'left_signatory' => 'Head of Department',
                'right_signatory' => 'Program Director',
                'primary_color' => '#1e4a8c',
                'accent_color' => '#c9a227',
                'paper_color' => '#fffef8',
            ],
            'emerald-elegance' => [
                'preset_key' => 'emerald-elegance',
                'theme' => 'emerald',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Achievement',
                'subtitle' => 'This is to certify that',
                'body' => 'has successfully completed the course and fulfilled all learning objectives with dedication and excellence.',
                'left_signatory' => 'Course Instructor',
                'right_signatory' => 'Academic Head',
                'primary_color' => '#047857',
                'accent_color' => '#f59e0b',
                'paper_color' => '#f0fdf4',
            ],
            'minimal-slate' => [
                'preset_key' => 'minimal-slate',
                'theme' => 'minimal',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Completion',
                'subtitle' => 'Awarded to',
                'body' => 'for the successful completion of the program and demonstration of required competencies.',
                'left_signatory' => 'Authorized Signatory',
                'right_signatory' => 'Institute Director',
                'primary_color' => '#334155',
                'accent_color' => '#94a3b8',
                'paper_color' => '#ffffff',
            ],
            'royal-navy' => [
                'preset_key' => 'royal-navy',
                'theme' => 'classic-blue-gold',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Excellence',
                'subtitle' => 'Presented with honor to',
                'body' => 'in recognition of outstanding performance and successful completion of all course requirements.',
                'left_signatory' => 'Dean of Studies',
                'right_signatory' => 'Institute Principal',
                'primary_color' => '#0f172a',
                'accent_color' => '#cbd5e1',
                'paper_color' => '#f8fafc',
            ],
            'teal-modern' => [
                'preset_key' => 'teal-modern',
                'theme' => 'emerald',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Completion',
                'subtitle' => 'This certifies that',
                'body' => 'has completed the course curriculum and met the standards required for certification.',
                'left_signatory' => 'Training Lead',
                'right_signatory' => 'Center Director',
                'primary_color' => '#0d9488',
                'accent_color' => '#7ac4be',
                'paper_color' => '#f0fdfa',
            ],
            'burgundy-heritage' => [
                'preset_key' => 'burgundy-heritage',
                'theme' => 'classic-blue-gold',
                'orientation' => 'A4-landscape',
                'title' => 'Certificate of Merit',
                'subtitle' => 'This certificate is awarded to',
                'body' => 'for distinguished accomplishment in completing the course and achieving the required proficiency.',
                'left_signatory' => 'Registrar',
                'right_signatory' => 'Vice Chancellor',
                'primary_color' => '#7f1d1d',
                'accent_color' => '#d4a574',
                'paper_color' => '#fffbf5',
            ],
            'forest-academy' => [
                'preset_key' => 'forest-academy',
                'theme' => 'emerald',
                'orientation' => 'A4-portrait',
                'title' => 'Certificate of Training',
                'subtitle' => 'This is to acknowledge that',
                'body' => 'has participated in and successfully completed the training program as prescribed by the institute.',
                'left_signatory' => 'Program Coordinator',
                'right_signatory' => 'Training Director',
                'primary_color' => '#166534',
                'accent_color' => '#86efac',
                'paper_color' => '#f7fee7',
            ],
            'indigo-executive' => [
                'preset_key' => 'indigo-executive',
                'theme' => 'minimal',
                'orientation' => 'A4-landscape',
                'title' => 'Professional Certificate',
                'subtitle' => 'Certified professional',
                'body' => 'has met all professional standards and successfully completed the executive learning program.',
                'left_signatory' => 'Managing Director',
                'right_signatory' => 'Chief Learning Officer',
                'primary_color' => '#4338ca',
                'accent_color' => '#a5b4fc',
                'paper_color' => '#eef2ff',
            ],
        ];

        $layout = $layouts[$key] ?? $layouts['classic-blue-gold'];
        $layout['show_verify_url'] = true;
        $layout['show_cert_number'] = true;

        return $layout;
    }

    public function resolvePresetKey(array $layout): string
    {
        $key = $layout['preset_key'] ?? null;
        if ($key && isset($this->presets()[$key])) {
            return $key;
        }

        foreach ($this->presets() as $presetKey => $preset) {
            $candidate = $preset['layout'];
            if (($layout['primary_color'] ?? null) === ($candidate['primary_color'] ?? null)
                && ($layout['theme'] ?? null) === ($candidate['theme'] ?? null)
                && ($layout['title'] ?? null) === ($candidate['title'] ?? null)) {
                return $presetKey;
            }
        }

        return 'classic-blue-gold';
    }

    public function applyPreset(CertificateTemplate $template, string $key): CertificateTemplate
    {
        if (! isset($this->presets()[$key])) {
            $key = 'classic-blue-gold';
        }

        $preset = $this->presets()[$key];
        $layout = $preset['layout'];
        $layout['element_positions'] = $this->defaultElementPositions();

        return $this->saveDesign($template, $layout, $preset['name'].' — '.$template->course?->title);
    }

    public function forCourse(Course $course): CertificateTemplate
    {
        $template = CertificateTemplate::where('course_id', $course->id)->latest('id')->first();

        if (! $template) {
            $settings = $course->settings;
            $config = $settings?->certificate_config ?? [];
            $templateId = $config['certificate_template_id'] ?? null;

            if ($templateId) {
                $existing = CertificateTemplate::find($templateId);
                if ($existing && (int) $existing->course_id === (int) $course->id) {
                    $template = $existing;
                }
            }
        }

        if (! $template) {
            $template = CertificateTemplate::create([
                'course_id' => $course->id,
                'name' => $course->title.' Certificate',
                'certificate_title' => 'Certificate of Completion',
                'html_content' => $this->compileHtml($this->defaultLayout()),
                'layout_config' => $this->defaultLayout(),
                'is_default' => false,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);
        }

        $this->ensureLayout($template);
        $this->attachToCourse($course, $template);

        return $template->fresh();
    }

    public function saveDesign(CertificateTemplate $template, array $layout, ?string $name = null): CertificateTemplate
    {
        $merged = array_merge($this->defaultLayout(), $layout);
        $merged['element_positions'] = $this->sanitizeElementPositions($merged['element_positions'] ?? []);

        $template->update([
            'name' => $name ?: ($template->name ?: 'Course Certificate'),
            'certificate_title' => $merged['title'] ?? 'Certificate of Completion',
            'layout_config' => $merged,
            'html_content' => $this->compileHtml($merged),
            'status' => 'active',
        ]);

        return $template->fresh();
    }

    public function attachToCourse(Course $course, CertificateTemplate $template): void
    {
        $settings = $course->settings()->firstOrCreate([]);
        $config = $settings->certificate_config ?? [];
        $config['certificate_template_id'] = $template->id;
        $settings->update(['certificate_config' => $config]);
    }

    public function replacements(Certificate $certificate): array
    {
        $certificate->loadMissing(['user', 'course']);
        $verifyUrl = url('/verify-certificate?number='.$certificate->certificate_number);

        return [
            'student_name' => $certificate->user?->name ?? 'Learner',
            'course_name' => $certificate->course?->title ?? 'Course',
            'cert_id' => $certificate->certificate_number,
            'verify_url' => $verifyUrl,
            'issue_date' => optional($certificate->issued_at)->format('F d, Y') ?? now()->format('F d, Y'),
            'institute_name' => config('app.name', 'StudyNest'),
        ];
    }

    public function previewReplacements(Course $course, ?User $user = null): array
    {
        return [
            'student_name' => $user?->name ?? 'Student Name',
            'course_name' => $course->title,
            'cert_id' => 'CERT-PREVIEW123',
            'verify_url' => url('/verify-certificate?number=CERT-PREVIEW123'),
            'issue_date' => now()->format('F d, Y'),
            'institute_name' => config('app.name', 'StudyNest'),
        ];
    }

    public function applyPlaceholders(string $content, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $content = str_replace(['{'.$key.'}', '{{'.$key.'}}'], (string) $value, $content);
        }

        return $content;
    }

    public function layoutFrom(CertificateTemplate $template): array
    {
        $layout = array_merge($this->defaultLayout(), $template->layout_config ?? []);
        $layout['element_positions'] = $this->sanitizeElementPositions($layout['element_positions'] ?? []);

        return $layout;
    }

    protected function ensureLayout(CertificateTemplate $template): CertificateTemplate
    {
        if (empty($template->layout_config)) {
            $template->update([
                'layout_config' => $this->defaultLayout(),
                'html_content' => $template->html_content ?: $this->compileHtml($this->defaultLayout()),
            ]);
        }

        return $template->fresh();
    }

    public function compileHtml(array $layout): string
    {
        $title = e($layout['title'] ?? 'Certificate of Completion');
        $subtitle = e($layout['subtitle'] ?? 'This Certificate is Proudly Present to:');
        $body = e($layout['body'] ?? '');
        $left = e($layout['left_signatory'] ?? 'Head of the Department');
        $right = e($layout['right_signatory'] ?? 'School Principal');

        $footer = '';
        if (! empty($layout['show_verify_url'])) {
            $footer .= '<div class="cert-footer-item">Verify at: <strong>{verify_url}</strong></div>';
        }
        if (! empty($layout['show_cert_number'])) {
            $footer .= '<div class="cert-footer-item">Certificate Number: <strong>{cert_id}</strong></div>';
        }

        $pt = $this->positionAttribute($layout, 'title');
        $ps = $this->positionAttribute($layout, 'subtitle');
        $pst = $this->positionAttribute($layout, 'student');
        $pb = $this->positionAttribute($layout, 'body');
        $pc = $this->positionAttribute($layout, 'course');
        $pls = $this->positionAttribute($layout, 'left_sign');
        $prs = $this->positionAttribute($layout, 'right_sign');
        $pf = $this->positionAttribute($layout, 'footer');

        return <<<HTML
<div class="cert-inner">
  <div class="cert-ornament cert-ornament-tl"></div>
  <div class="cert-ornament cert-ornament-br"></div>
  <p class="cert-title"{$pt}>{$title}</p>
  <p class="cert-subtitle"{$ps}>{$subtitle}</p>
  <p class="cert-student"{$pst}>{student_name}</p>
  <p class="cert-body"{$pb}>{$body}</p>
  <p class="cert-course"{$pc}>{course_name}</p>
  <div class="cert-signs">
    <div class="cert-sign"{$pls}><div class="cert-sign-line"></div><span>{$left}</span></div>
    <div class="cert-sign"{$prs}><div class="cert-sign-line"></div><span>{$right}</span></div>
  </div>
  <div class="cert-footer"{$pf}>{$footer}</div>
</div>
HTML;
    }
}

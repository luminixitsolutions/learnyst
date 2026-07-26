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

    public function defaultLayout(): array
    {
        return [
            'theme' => 'classic-blue-gold',
            'orientation' => 'A4-landscape',
            'title' => 'Certificate of Completion',
            'subtitle' => 'This Certificate is Proudly Present to:',
            'body' => 'for successfully completing all requirements of the course and demonstrating the required knowledge and skills.',
            'left_signatory' => 'Head of the Department',
            'right_signatory' => 'School Principal',
            'show_verify_url' => true,
            'show_cert_number' => true,
            'primary_color' => '#1e4a8c',
            'accent_color' => '#c9a227',
            'paper_color' => '#fffef8',
        ];
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
            'institute_name' => config('app.name', 'Learnyst'),
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
            'institute_name' => config('app.name', 'Learnyst'),
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
        return array_merge($this->defaultLayout(), $template->layout_config ?? []);
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

        return <<<HTML
<div class="cert-ornament cert-ornament-tl"></div>
<div class="cert-ornament cert-ornament-br"></div>
<div class="cert-inner">
  <p class="cert-title">{$title}</p>
  <p class="cert-subtitle">{$subtitle}</p>
  <p class="cert-student">{student_name}</p>
  <p class="cert-body">{$body}</p>
  <p class="cert-course">{course_name}</p>
  <div class="cert-signs">
    <div class="cert-sign"><div class="cert-sign-line"></div><span>{$left}</span></div>
    <div class="cert-sign"><div class="cert-sign-line"></div><span>{$right}</span></div>
  </div>
  <div class="cert-footer">{$footer}</div>
</div>
HTML;
    }
}

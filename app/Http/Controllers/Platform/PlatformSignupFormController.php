<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\SignupFormService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformSignupFormController extends Controller
{
    public function index()
    {
        $questions = collect(SignupFormService::questions())
            ->map(function ($meta, $key) {
                $content = SignupFormService::get($key);
                $row = WebsiteContent::query()->where('key', SignupFormService::contentKey($key))->first();

                return array_merge($meta, [
                    'key' => $key,
                    'title' => $content['title'] ?? $meta['label'],
                    'option_count' => count(array_filter(
                        $content['options'] ?? [],
                        fn ($o) => ($o['is_active'] ?? true)
                    )),
                    'is_customized' => (bool) $row,
                    'updated_at' => $row?->updated_at,
                ]);
            })
            ->sortBy('sort')
            ->values();

        return view('platform.signup-form.index', compact('questions'));
    }

    public function edit(string $signupQuestion)
    {
        abort_unless(array_key_exists($signupQuestion, SignupFormService::questions()), 404);

        $question = $signupQuestion;
        $meta = SignupFormService::questions()[$question];
        $content = SignupFormService::get($question);

        return view('platform.signup-form.edit', compact('question', 'meta', 'content'));
    }

    public function update(Request $request, string $signupQuestion)
    {
        abort_unless(array_key_exists($signupQuestion, SignupFormService::questions()), 404);

        $question = $signupQuestion;
        $meta = SignupFormService::questions()[$question];

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1000'],
            'label' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
            'options.*.value' => ['nullable', 'string', 'max:80'],
            'options.*.label' => ['nullable', 'string', 'max:255'],
            'options.*.is_active' => ['nullable'],
            'options.*.opens_teach' => ['nullable'],
        ]);

        $options = [];
        $usedValues = [];

        foreach ($validated['options'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $value = trim((string) ($row['value'] ?? ''));
            if ($value === '') {
                $value = SignupFormService::slugify($label);
            }
            $value = Str::slug($value, '_');

            if (isset($usedValues[$value])) {
                $value .= '_'.substr(md5($label.microtime()), 0, 4);
            }
            $usedValues[$value] = true;

            $option = [
                'value' => $value,
                'label' => $label,
                'is_active' => ! empty($row['is_active']),
            ];

            if ($question === 'business_type') {
                $option['opens_teach'] = ! empty($row['opens_teach']);
            }

            $options[] = $option;
        }

        if (count($options) < 1) {
            return back()->withErrors(['options' => 'Add at least one option.'])->withInput();
        }

        WebsiteContent::putContent(
            SignupFormService::contentKey($question),
            $meta['label'],
            [
                'title' => $validated['title'],
                'subtitle' => $validated['subtitle'] ?? null,
                'label' => $validated['label'] ?? null,
                'options' => $options,
            ],
            'signup',
            $meta['sort']
        );

        ActivityLogger::log('signup_form_updated', "Signup form updated: {$meta['label']}");

        return redirect()
            ->route('platform.signup-form.edit', $question)
            ->with('success', "{$meta['label']} options saved.");
    }

    public function reset(string $signupQuestion)
    {
        abort_unless(array_key_exists($signupQuestion, SignupFormService::questions()), 404);

        $question = $signupQuestion;
        $meta = SignupFormService::questions()[$question];
        $key = SignupFormService::contentKey($question);
        WebsiteContent::query()->where('key', $key)->delete();
        cache()->forget("website_content.{$key}");

        ActivityLogger::log('signup_form_reset', "Signup form reset: {$meta['label']}");

        return redirect()
            ->route('platform.signup-form.edit', $question)
            ->with('success', "{$meta['label']} reset to defaults.");
    }
}

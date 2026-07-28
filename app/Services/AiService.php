<?php

namespace App\Services;

use App\Models\AiGeneration;
use App\Models\Setting;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AiService
{
    public function isEnabledFor(?int $instituteUserId = null): bool
    {
        $flag = Setting::get('ai_enabled', '1', 'ai');
        if ($flag === '0' || $flag === false) {
            return false;
        }

        // Premium gate: if packages mention AI feature, require at least one active package with ai in features (soft check)
        $packages = SubscriptionPackage::active()->get();
        if ($packages->isEmpty()) {
            return true;
        }

        $aiPackages = $packages->filter(function (SubscriptionPackage $p) {
            $features = strtolower(implode(' ', $p->featureList()));

            return str_contains($features, 'ai') || ($p->meta['ai_enabled'] ?? false);
        });

        // If no package advertises AI, allow; if some do, allow institutes by default (billing not enforced here)
        return true;
    }

    public function getConfig(?int $instituteUserId = null): array
    {
        $group = $instituteUserId ? 'ai_'.$instituteUserId : 'ai';
        $keyEncrypted = Setting::get('api_key', null, $group) ?: Setting::get('api_key', null, 'ai');
        $apiKey = null;
        if ($keyEncrypted) {
            try {
                $apiKey = Crypt::decryptString($keyEncrypted);
            } catch (\Throwable) {
                $apiKey = $keyEncrypted;
            }
        }

        return [
            'base_url' => rtrim((string) (Setting::get('base_url', 'https://api.openai.com/v1', $group) ?: Setting::get('base_url', 'https://api.openai.com/v1', 'ai')), '/'),
            'api_key' => $apiKey,
            'model' => Setting::get('model', 'gpt-4o-mini', $group) ?: Setting::get('model', 'gpt-4o-mini', 'ai'),
            'enabled' => $this->isEnabledFor($instituteUserId),
        ];
    }

    public function saveConfig(array $data, ?int $instituteUserId = null): void
    {
        $group = $instituteUserId ? 'ai_'.$instituteUserId : 'ai';

        if (! empty($data['base_url'])) {
            Setting::set('base_url', rtrim($data['base_url'], '/'), $group);
        }
        if (! empty($data['model'])) {
            Setting::set('model', $data['model'], $group);
        }
        if (array_key_exists('enabled', $data)) {
            Setting::set('ai_enabled', $data['enabled'] ? '1' : '0', $group);
        }
        if (! empty($data['api_key'])) {
            Setting::set('api_key', Crypt::encryptString($data['api_key']), $group, 'encrypted');
        }
    }

    public function generate(User $user, string $feature, string $prompt, ?int $instituteUserId = null, ?int $courseId = null, ?string $title = null): AiGeneration
    {
        $instituteUserId = $instituteUserId ?: $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $system = $this->systemPrompt($feature);
        $output = $feature === 'course_details'
            ? $this->chatJson($config, $system, $prompt)
            : $this->chat($config, $system, $prompt);

        return AiGeneration::create([
            'created_by' => $instituteUserId,
            'user_id' => $user->id,
            'feature' => $feature,
            'title' => $title ?: ucfirst(str_replace('_', ' ', $feature)),
            'prompt' => $prompt,
            'output' => is_array($output) ? json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $output,
            'status' => 'draft',
            'course_id' => $courseId,
            'meta' => ['model' => $config['model'], 'structured' => $feature === 'course_details'],
        ]);
    }

    /**
     * Generate structured course form fields from a title / brief.
     *
     * @param  array<int, array{id:int,name:string}>  $categories
     * @param  array<int, array{id:int,name:string}>  $tags
     * @return array<string, mixed>
     */
    public function generateCourseDetails(User $user, string $title, ?string $brief = null, array $categories = [], array $tags = []): array
    {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $categoryList = collect($categories)->map(fn ($c) => ($c['id'] ?? '').': '.($c['name'] ?? ''))->implode(', ');
        $tagList = collect($tags)->map(fn ($t) => ($t['id'] ?? '').': '.($t['name'] ?? ''))->implode(', ');

        $prompt = "Course title: {$title}\n"
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            ."Available categories (id:name): {$categoryList}\n"
            ."Available tags (id:name): {$tagList}\n"
            ."Return ONLY valid JSON with keys: "
            ."subtitle, description, seo_title, seo_description, product_type, access_type, "
            ."price, sale_price, validity_days, category_id, tag_ids, is_free, suggested_outline.\n"
            ."Rules: product_type one of course|ebook|podcast|webinar|custom|free_resource; "
            ."access_type one of free|trial|paid; price/sale_price numbers in INR; "
            ."validity_days integer or null; category_id must be one of the provided ids or null; "
            ."tag_ids array of provided tag ids (0-5); is_free boolean; "
            ."description 2-4 short paragraphs; suggested_outline array of module titles.";

        $data = $this->chatJson($config, $this->systemPrompt('course_details'), $prompt);

        return $this->normalizeCourseDetails($data, $title, $categories, $tags);
    }

    /**
     * Generate structured assignment form fields from a title / brief.
     *
     * @return array<string, mixed>
     */
    public function generateAssignmentDetails(User $user, string $title, ?string $brief = null, ?string $courseTitle = null): array
    {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $today = now()->toDateString();
        $prompt = "Assignment title: {$title}\n"
            .($courseTitle ? "Course context: {$courseTitle}\n" : '')
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            ."Today's date: {$today}\n"
            ."Return ONLY valid JSON with keys: "
            ."description, marks, due_date, status, rubric_points.\n"
            ."Rules: description is a full assignment brief (objectives, tasks, submission guidelines) as plain text with short paragraphs; "
            ."marks is a number (typically 10–100); due_date is YYYY-MM-DD about 7–14 days from today; "
            ."status is draft or published (prefer draft); "
            ."rubric_points is an array of short strings (3–6 grading criteria).";

        $data = $this->chatJson(
            $config,
            $this->systemPrompt('assignment_details'),
            $prompt,
            fn (string $p, ?string $reason = null) => $this->offlineAssignmentDetailsFallback($p, $reason)
        );

        return $this->normalizeAssignmentDetails($data, $title);
    }

    /**
     * Generate structured quiz form fields and MCQs from a title / brief.
     *
     * @return array<string, mixed>
     */
    public function generateQuizDetails(User $user, string $title, ?string $brief = null, ?string $courseTitle = null, int $questionCount = 5): array
    {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $questionCount = max(3, min(15, $questionCount));

        $prompt = "Quiz title: {$title}\n"
            .($courseTitle ? "Course context: {$courseTitle}\n" : '')
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            ."Generate exactly {$questionCount} multiple-choice questions.\n"
            ."Return ONLY valid JSON with keys: "
            ."total_marks, passing_marks, time_limit, questions.\n"
            ."Rules: total_marks is sum of question marks (number); passing_marks about 40–60% of total; "
            ."time_limit is minutes (integer, typically 15–60); "
            ."questions is an array of objects with keys: question (string), options (array of exactly 4 strings), "
            ."correct (0-based index of the correct option), marks (number, typically 1–5).";

        $data = $this->chatJson(
            $config,
            $this->systemPrompt('quiz_details'),
            $prompt,
            fn (string $p, ?string $reason = null) => $this->offlineQuizDetailsFallback($p, $reason, $questionCount)
        );

        return $this->normalizeQuizDetails($data, $title, $questionCount);
    }

    /**
     * Generate structured live-class form fields from a title / brief.
     *
     * @param  array<int, array{id:int,title:string}>  $courses
     * @return array<string, mixed>
     */
    public function generateLiveClassDetails(
        User $user,
        string $title,
        ?string $brief = null,
        ?string $courseTitle = null,
        array $courses = []
    ): array {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $today = now()->toDateString();
        $courseList = collect($courses)->map(fn ($c) => ($c['id'] ?? '').': '.($c['title'] ?? ''))->implode(', ');

        $prompt = "Live class title: {$title}\n"
            .($courseTitle ? "Selected course: {$courseTitle}\n" : '')
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            ."Today's date: {$today}\n"
            ."Available courses (id:title): {$courseList}\n"
            ."Return ONLY valid JSON with keys: "
            ."description, platform, starts_at, start_time, end_time, status, course_id, agenda_points.\n"
            ."Rules: description is a class brief with outcomes and prep notes (plain text paragraphs); "
            ."platform one of zoom|google_meet|youtube|microsoft_teams|other (prefer zoom); "
            ."starts_at is YYYY-MM-DD on the next suitable weekday (not in the past); "
            ."start_time and end_time are HH:MM in 24h (typical 45–90 minute session, evening-friendly for India e.g. 18:00–19:30); "
            ."status must be scheduled; course_id must be one of the provided ids or null; "
            ."agenda_points is an array of 4–7 short agenda strings. Do not invent meeting URLs.";

        $data = $this->chatJson(
            $config,
            $this->systemPrompt('live_class_details'),
            $prompt,
            fn (string $p, ?string $reason = null) => $this->offlineLiveClassDetailsFallback($p, $reason)
        );

        return $this->normalizeLiveClassDetails($data, $title, $courses);
    }

    /**
     * Generate an instructor bio from name / profession.
     *
     * @return array<string, mixed>
     */
    public function generateInstructorBio(
        User $user,
        string $name,
        string $profession,
        ?string $brief = null
    ): array {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $prompt = "Instructor name: {$name}\n"
            ."Profession: {$profession}\n"
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            ."Return ONLY valid JSON with keys: bio, highlight_points.\n"
            ."Rules: bio is a warm, credible 2–4 short paragraph instructor bio for an LMS profile "
            ."(third person, mention the profession clearly, teaching style, and who learners are); "
            ."highlight_points is an array of 3–5 short strengths related to the profession. "
            ."Do not invent fake degrees, employers, or years of experience unless implied by the notes.";

        $data = $this->chatJson(
            $config,
            $this->systemPrompt('instructor_bio'),
            $prompt,
            fn (string $p, ?string $reason = null) => $this->offlineInstructorBioFallback($p, $reason)
        );

        return $this->normalizeInstructorBio($data, $name, $profession);
    }

    /**
     * Generate sub-admin profile notes from name / designation.
     *
     * @return array<string, mixed>
     */
    public function generateSubAdminDetails(
        User $user,
        string $name,
        string $designation,
        ?string $brief = null,
        ?string $emailDomain = null
    ): array {
        $instituteUserId = $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $prompt = "Sub-admin name: {$name}\n"
            ."Designation / role title: {$designation}\n"
            .($brief ? "Extra notes from admin: {$brief}\n" : '')
            .($emailDomain ? "Institute email domain: {$emailDomain}\n" : '')
            ."Return ONLY valid JSON with keys: bio, suggested_email, responsibility_points.\n"
            ."Rules: bio is a short 2–3 paragraph internal staff profile (third person) describing how this "
            ."{$designation} supports the institute LMS operations; "
            ."suggested_email is a professional email using the provided domain when available "
            ."(format firstname.lastname@domain, lowercase, no spaces); "
            ."responsibility_points is an array of 3–6 short duty bullets for a {$designation}. "
            ."Do not invent fake employers or credentials.";

        $data = $this->chatJson(
            $config,
            $this->systemPrompt('sub_admin_details'),
            $prompt,
            fn (string $p, ?string $reason = null) => $this->offlineSubAdminDetailsFallback($p, $reason, $emailDomain)
        );

        return $this->normalizeSubAdminDetails($data, $name, $designation, $emailDomain);
    }

    public function chat(array $config, string $system, string $userMessage, array $history = []): string
    {
        if (empty($config['api_key'])) {
            return $this->offlineFallback($system, $userMessage);
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $system]],
            $history,
            [['role' => 'user', 'content' => $userMessage]]
        );

        try {
            $response = Http::withToken($config['api_key'])
                ->timeout(60)
                ->post($config['base_url'].'/chat/completions', [
                    'model' => $config['model'],
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                return $this->offlineFallback($system, $userMessage, 'API error: '.$response->status());
            }

            return (string) data_get($response->json(), 'choices.0.message.content', '');
        } catch (\Throwable $e) {
            return $this->offlineFallback($system, $userMessage, $e->getMessage());
        }
    }

    /**
     * @param  (callable(string, ?string): array<string, mixed>)|null  $offlineFallback
     * @return array<string, mixed>
     */
    public function chatJson(array $config, string $system, string $userMessage, ?callable $offlineFallback = null): array
    {
        $fallback = $offlineFallback ?? fn (string $prompt, ?string $reason = null) => $this->offlineCourseDetailsFallback($prompt, $reason);

        if (empty($config['api_key'])) {
            return $fallback($userMessage);
        }

        try {
            $response = Http::withToken($config['api_key'])
                ->timeout(60)
                ->post($config['base_url'].'/chat/completions', [
                    'model' => $config['model'],
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.5,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (! $response->successful()) {
                return $fallback($userMessage, 'API error: '.$response->status());
            }

            $content = (string) data_get($response->json(), 'choices.0.message.content', '');
            $decoded = $this->extractJson($content);

            return $decoded ?: $fallback($userMessage, 'Invalid JSON from model');
        } catch (\Throwable $e) {
            return $fallback($userMessage, $e->getMessage());
        }
    }

    protected function systemPrompt(string $feature): string
    {
        return match ($feature) {
            'course_outline' => 'You are an expert instructional designer. Produce a clear course outline with modules and lessons.',
            'course_details' => 'You are an LMS course marketing and instructional design expert for Indian institutes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'assignment_details' => 'You are an educator designing graded LMS assignments for Indian institutes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'quiz_details' => 'You are an exam setter for Indian LMS institutes. Create fair multiple-choice quizzes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'live_class_details' => 'You are an instructional coordinator scheduling live online classes for Indian institutes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'instructor_bio' => 'You are a professional profile writer for LMS instructor pages at Indian institutes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'sub_admin_details' => 'You are an HR/ops assistant writing short LMS sub-admin staff profiles for Indian institutes. Reply with a single JSON object only — no markdown fences, no commentary.',
            'quiz' => 'You are an exam setter. Generate multiple-choice questions with options and correct answers marked.',
            'notes' => 'You are a tutor. Produce concise study notes with headings and bullet points.',
            'assignment' => 'You are an educator. Create a practical assignment with objectives, tasks, and rubric.',
            'doubt_chat' => 'You are a helpful teaching assistant. Answer the learner doubt clearly and briefly.',
            'study_planner' => 'You are a study coach. Build a realistic day-by-day study plan.',
            'performance' => 'You are a learning analyst. Suggest improvements based on the learner context provided.',
            default => 'You are a helpful education assistant for an LMS.',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{id:int,name:string}>  $categories
     * @param  array<int, array{id:int,name:string}>  $tags
     * @return array<string, mixed>
     */
    protected function normalizeCourseDetails(array $data, string $title, array $categories, array $tags): array
    {
        $categoryIds = collect($categories)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $tagIds = collect($tags)->pluck('id')->map(fn ($id) => (int) $id)->all();

        $categoryId = isset($data['category_id']) ? (int) $data['category_id'] : null;
        if ($categoryId && ! in_array($categoryId, $categoryIds, true)) {
            $categoryId = null;
        }

        $selectedTags = collect($data['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $tagIds, true))
            ->unique()
            ->values()
            ->all();

        $productType = (string) ($data['product_type'] ?? 'course');
        if (! in_array($productType, ['course', 'ebook', 'podcast', 'webinar', 'custom', 'free_resource'], true)) {
            $productType = 'course';
        }

        $accessType = (string) ($data['access_type'] ?? 'paid');
        if (! in_array($accessType, ['free', 'trial', 'paid'], true)) {
            $accessType = 'paid';
        }

        $isFree = (bool) ($data['is_free'] ?? ($accessType === 'free'));
        $price = (float) ($data['price'] ?? ($isFree ? 0 : 2999));
        $salePrice = $data['sale_price'] ?? null;

        return [
            'title' => $title,
            'subtitle' => (string) ($data['subtitle'] ?? Str::limit('Master '.$title.' with practical, mentor-led learning.', 120, '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'seo_title' => (string) ($data['seo_title'] ?? Str::limit($title.' | Online Course', 60, '')),
            'seo_description' => (string) ($data['seo_description'] ?? Str::limit(strip_tags((string) ($data['description'] ?? '')), 155, '')),
            'product_type' => $productType,
            'access_type' => $isFree ? 'free' : $accessType,
            'price' => $isFree ? 0 : max(0, round($price, 2)),
            'sale_price' => $salePrice !== null && $salePrice !== '' ? max(0, round((float) $salePrice, 2)) : null,
            'validity_days' => isset($data['validity_days']) && $data['validity_days'] !== '' ? (int) $data['validity_days'] : 365,
            'category_id' => $categoryId,
            'tag_ids' => $selectedTags,
            'is_free' => $isFree,
            'suggested_outline' => array_values(array_filter((array) ($data['suggested_outline'] ?? []))),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeAssignmentDetails(array $data, string $title): array
    {
        $marks = isset($data['marks']) ? (float) $data['marks'] : 100;
        $marks = max(1, min(1000, round($marks, 2)));

        $dueDate = (string) ($data['due_date'] ?? '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate) || strtotime($dueDate) === false) {
            $dueDate = now()->addDays(10)->toDateString();
        }

        $status = (string) ($data['status'] ?? 'draft');
        if (! in_array($status, ['draft', 'published'], true)) {
            $status = 'draft';
        }

        $description = trim((string) ($data['description'] ?? ''));
        if ($description === '') {
            $description = "Complete the assignment: {$title}.\n\n"
                ."Objectives:\n- Demonstrate understanding of the topic\n- Apply concepts through a practical task\n\n"
                ."Tasks:\n1. Read the relevant lesson materials\n2. Complete the required deliverable\n3. Submit before the due date\n\n"
                .'Submission: Upload your work as a single file (PDF/DOC/ZIP as instructed).';
        }

        return [
            'title' => $title,
            'description' => $description,
            'marks' => $marks,
            'due_date' => $dueDate,
            'status' => $status,
            'rubric_points' => array_values(array_filter(array_map(
                fn ($item) => trim((string) $item),
                (array) ($data['rubric_points'] ?? [])
            ))),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeQuizDetails(array $data, string $title, int $questionCount = 5): array
    {
        $questions = [];
        foreach ((array) ($data['questions'] ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $options = array_values(array_map(
                fn ($o) => trim((string) $o),
                array_slice((array) ($item['options'] ?? []), 0, 4)
            ));
            while (count($options) < 4) {
                $options[] = 'Option '.chr(65 + count($options));
            }

            $correct = isset($item['correct']) ? (int) $item['correct'] : 0;
            if ($correct < 0 || $correct > 3) {
                $correct = 0;
            }

            $marks = isset($item['marks']) ? (float) $item['marks'] : 1;
            $marks = max(0.5, min(20, round($marks, 2)));

            $questionText = trim((string) ($item['question'] ?? $item['text'] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $questions[] = [
                'question' => $questionText,
                'options' => $options,
                'correct' => $correct,
                'marks' => $marks,
            ];
        }

        if ($questions === []) {
            $fallback = $this->offlineQuizDetailsFallback("Quiz title: {$title}", null, $questionCount);
            $questions = $fallback['questions'];
        }

        $computedTotal = array_sum(array_column($questions, 'marks'));
        $totalMarks = isset($data['total_marks']) ? (float) $data['total_marks'] : $computedTotal;
        $totalMarks = max(1, round($totalMarks ?: $computedTotal, 2));

        $passing = isset($data['passing_marks']) ? (float) $data['passing_marks'] : round($totalMarks * 0.5, 2);
        $passing = max(0, min($totalMarks, round($passing, 2)));

        $timeLimit = isset($data['time_limit']) ? (int) $data['time_limit'] : max(15, count($questions) * 2);
        $timeLimit = max(5, min(180, $timeLimit));

        return [
            'title' => $title,
            'total_marks' => $totalMarks,
            'passing_marks' => $passing,
            'time_limit' => $timeLimit,
            'questions' => $questions,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{id:int,title:string}>  $courses
     * @return array<string, mixed>
     */
    protected function normalizeLiveClassDetails(array $data, string $title, array $courses = []): array
    {
        $courseIds = collect($courses)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $courseId = isset($data['course_id']) ? (int) $data['course_id'] : null;
        if ($courseId && ! in_array($courseId, $courseIds, true)) {
            $courseId = null;
        }

        $platform = (string) ($data['platform'] ?? 'zoom');
        if (! in_array($platform, ['zoom', 'google_meet', 'youtube', 'microsoft_teams', 'other'], true)) {
            $platform = 'zoom';
        }

        $startsAt = (string) ($data['starts_at'] ?? '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $startsAt) || strtotime($startsAt) === false) {
            $startsAt = now()->next('Monday')->toDateString();
            if (now()->isMonday() && now()->hour < 12) {
                $startsAt = now()->toDateString();
            }
        }
        if (strtotime($startsAt) < strtotime(now()->toDateString())) {
            $startsAt = now()->addDay()->toDateString();
        }

        $startTime = (string) ($data['start_time'] ?? '18:00');
        if (! preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime = '18:00';
        }

        $endTime = (string) ($data['end_time'] ?? '19:00');
        if (! preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime = '19:00';
        }
        if (strtotime($endTime) <= strtotime($startTime)) {
            $endTime = date('H:i', strtotime($startTime.' +60 minutes'));
        }

        $description = trim((string) ($data['description'] ?? ''));
        if ($description === '') {
            $description = "Live session: {$title}\n\n"
                ."Join this interactive class to cover key concepts, work through examples, and clear doubts.\n\n"
                ."Please join 5 minutes early and keep your notes ready.";
        }

        return [
            'title' => $title,
            'description' => $description,
            'platform' => $platform,
            'starts_at' => $startsAt,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'scheduled',
            'course_id' => $courseId,
            'agenda_points' => array_values(array_filter(array_map(
                fn ($item) => trim((string) $item),
                (array) ($data['agenda_points'] ?? [])
            ))),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeInstructorBio(array $data, string $name, string $profession): array
    {
        $bio = trim((string) ($data['bio'] ?? ''));
        if ($bio === '') {
            $fallback = $this->offlineInstructorBioFallback(
                "Instructor name: {$name}\nProfession: {$profession}",
                null
            );
            $bio = $fallback['bio'];
            $highlights = $fallback['highlight_points'];
        } else {
            $highlights = array_values(array_filter(array_map(
                fn ($item) => trim((string) $item),
                (array) ($data['highlight_points'] ?? [])
            )));
        }

        return [
            'name' => $name,
            'profession' => $profession,
            'bio' => $bio,
            'highlight_points' => $highlights,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeSubAdminDetails(array $data, string $name, string $designation, ?string $emailDomain = null): array
    {
        $bio = trim((string) ($data['bio'] ?? ''));
        $responsibilities = array_values(array_filter(array_map(
            fn ($item) => trim((string) $item),
            (array) ($data['responsibility_points'] ?? [])
        )));

        if ($bio === '') {
            $fallback = $this->offlineSubAdminDetailsFallback(
                "Sub-admin name: {$name}\nDesignation / role title: {$designation}",
                null,
                $emailDomain
            );
            $bio = $fallback['bio'];
            $responsibilities = $fallback['responsibility_points'];
            $suggestedEmail = $fallback['suggested_email'];
        } else {
            $suggestedEmail = strtolower(trim((string) ($data['suggested_email'] ?? '')));
        }

        if ($suggestedEmail === '' || ! filter_var($suggestedEmail, FILTER_VALIDATE_EMAIL)) {
            $suggestedEmail = $this->suggestStaffEmail($name, $emailDomain);
        }

        return [
            'name' => $name,
            'designation' => $designation,
            'bio' => $bio,
            'suggested_email' => $suggestedEmail,
            'responsibility_points' => $responsibilities,
        ];
    }

    protected function suggestStaffEmail(string $name, ?string $emailDomain = null): string
    {
        $parts = preg_split('/\s+/', strtolower(trim($name))) ?: [];
        $parts = array_values(array_filter($parts, fn ($p) => $p !== ''));
        $local = count($parts) >= 2
            ? preg_replace('/[^a-z0-9]/', '', $parts[0]).'.'.preg_replace('/[^a-z0-9]/', '', end($parts))
            : preg_replace('/[^a-z0-9]/', '', implode('', $parts));
        $local = $local ?: 'staff';
        $domain = $emailDomain ? strtolower(ltrim($emailDomain, '@')) : 'institute.com';

        return $local.'@'.$domain;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function extractJson(string $content): ?array
    {
        $content = trim($content);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $content, $m)) {
            $content = trim($m[1]);
        }

        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $content, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineCourseDetailsFallback(string $prompt, ?string $reason = null): array
    {
        preg_match('/Course title:\s*(.+)/i', $prompt, $m);
        $title = trim($m[1] ?? 'New Course');
        $title = preg_replace('/\s+/', ' ', $title) ?: 'New Course';

        preg_match_all('/(\d+):\s*([^,]+)/', $prompt, $catMatches, PREG_SET_ORDER);
        $firstCategoryId = null;
        $firstTagIds = [];
        foreach ($catMatches as $i => $match) {
            if ($i === 0) {
                $firstCategoryId = (int) $match[1];
            }
            if ($i < 3 && str_contains(strtolower($prompt), 'tags')) {
                // best-effort: ignore, tags handled after categories section
            }
        }

        // Prefer first category id from "Available categories" section
        if (preg_match('/Available categories.*?:\s*(.+)\nAvailable tags/is', $prompt, $sec)) {
            if (preg_match('/(\d+):/', $sec[1], $cm)) {
                $firstCategoryId = (int) $cm[1];
            }
        }
        if (preg_match('/Available tags.*?:\s*(.+)\nReturn ONLY/is', $prompt, $sec)) {
            if (preg_match_all('/(\d+):/', $sec[1], $tm)) {
                $firstTagIds = array_map('intval', array_slice($tm[1], 0, 3));
            }
        }

        $outline = [
            'Introduction & course overview',
            'Core concepts of '.$title,
            'Hands-on practice & projects',
            'Assessments & feedback',
            'Next steps & certification prep',
        ];

        $description = "Learn {$title} through a structured, practical curriculum designed for working professionals and students.\n\n"
            ."This course covers fundamentals to advanced application with clear explanations, examples, and practice tasks.\n\n"
            ."By the end, you will be able to apply {$title} concepts confidently in real-world scenarios."
            .($reason ? "\n\n(AI draft note: {$reason})" : "\n\n(AI draft — configure API key in AI Center for richer generation.)");

        return [
            'subtitle' => 'Practical, mentor-style learning path for '.$title,
            'description' => $description,
            'seo_title' => Str::limit($title.' | Online Course', 60, ''),
            'seo_description' => Str::limit("Enroll in {$title}. Structured lessons, practice tasks, and outcomes-focused learning.", 155, ''),
            'product_type' => 'course',
            'access_type' => 'paid',
            'price' => 2999,
            'sale_price' => 1999,
            'validity_days' => 365,
            'category_id' => $firstCategoryId,
            'tag_ids' => $firstTagIds,
            'is_free' => false,
            'suggested_outline' => $outline,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineAssignmentDetailsFallback(string $prompt, ?string $reason = null): array
    {
        preg_match('/Assignment title:\s*(.+)/i', $prompt, $m);
        $title = trim($m[1] ?? 'New Assignment');
        $title = preg_replace('/\s+/', ' ', $title) ?: 'New Assignment';

        $note = $reason
            ? "\n\n(AI draft note: {$reason})"
            : "\n\n(AI draft — configure API key in AI Center for richer generation.)";

        $description = "Assignment: {$title}\n\n"
            ."Learning objectives:\n"
            ."- Apply the concepts covered in class to a practical task\n"
            ."- Demonstrate clear reasoning and structured presentation\n"
            ."- Meet the submission guidelines and deadline\n\n"
            ."Tasks:\n"
            ."1. Review the related lesson materials for {$title}\n"
            ."2. Complete the practical deliverable described by your instructor\n"
            ."3. Self-check against the rubric below before submitting\n\n"
            ."Submission guidelines:\n"
            ."- Submit one file (PDF preferred) with your name and assignment title\n"
            ."- Late submissions may receive reduced marks unless approved\n"
            .$note;

        return [
            'description' => $description,
            'marks' => 100,
            'due_date' => now()->addDays(10)->toDateString(),
            'status' => 'draft',
            'rubric_points' => [
                'Clarity and structure of the submission',
                'Accuracy of concepts applied',
                'Completeness of required tasks',
                'Originality and effort',
                'On-time submission',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineQuizDetailsFallback(string $prompt, ?string $reason = null, int $questionCount = 5): array
    {
        preg_match('/Quiz title:\s*(.+)/i', $prompt, $m);
        $title = trim($m[1] ?? 'New Quiz');
        $title = preg_replace('/\s+/', ' ', $title) ?: 'New Quiz';
        $questionCount = max(3, min(15, $questionCount));

        $questions = [];
        for ($i = 1; $i <= $questionCount; $i++) {
            $questions[] = [
                'question' => "Q{$i}. Which statement best relates to {$title}?",
                'options' => [
                    "Core concept #{$i} of {$title}",
                    "Unrelated fact about another topic",
                    "Incorrect definition of {$title}",
                    "Outdated practice for {$title}",
                ],
                'correct' => 0,
                'marks' => 2,
            ];
        }

        $total = $questionCount * 2;

        return [
            'total_marks' => $total,
            'passing_marks' => (int) ceil($total * 0.5),
            'time_limit' => max(15, $questionCount * 2),
            'questions' => $questions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineLiveClassDetailsFallback(string $prompt, ?string $reason = null): array
    {
        preg_match('/Live class title:\s*(.+)/i', $prompt, $m);
        $title = trim($m[1] ?? 'Live Class');
        $title = preg_replace('/\s+/', ' ', $title) ?: 'Live Class';

        $note = $reason
            ? "\n\n(AI draft note: {$reason})"
            : "\n\n(AI draft — configure API key in AI Center for richer generation.)";

        $startsAt = now()->next('Tuesday');
        if (now()->isTuesday() && (int) now()->format('H') < 12) {
            $startsAt = now();
        }

        $description = "Live class: {$title}\n\n"
            ."What we will cover:\n"
            ."- Core concepts related to {$title}\n"
            ."- Worked examples and common pitfalls\n"
            ."- Q&A and doubt clearing\n\n"
            ."Preparation:\n"
            ."- Review the previous lesson notes\n"
            ."- Join 5 minutes early with a stable internet connection\n"
            .$note;

        return [
            'description' => $description,
            'platform' => 'zoom',
            'starts_at' => $startsAt->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'status' => 'scheduled',
            'course_id' => null,
            'agenda_points' => [
                'Welcome & session objectives (5 min)',
                'Concept walkthrough for '.$title,
                'Live demo / worked example',
                'Practice prompts for learners',
                'Doubt clearing & wrap-up',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineInstructorBioFallback(string $prompt, ?string $reason = null): array
    {
        preg_match('/Instructor name:\s*(.+)/i', $prompt, $nm);
        preg_match('/Profession:\s*(.+)/i', $prompt, $pm);
        $name = trim($nm[1] ?? 'This instructor');
        $name = preg_replace('/\s+/', ' ', $name) ?: 'This instructor';
        $profession = trim($pm[1] ?? 'Educator');
        $profession = preg_replace('/\s+/', ' ', $profession) ?: 'Educator';

        $note = $reason
            ? "\n\n(AI draft note: {$reason})"
            : "\n\n(AI draft — configure API key in AI Center for richer generation.)";

        $bio = "{$name} is a {$profession} who enjoys helping learners build practical skills with clear explanations and hands-on practice.\n\n"
            ."As a {$profession}, {$name} focuses on real-world examples, structured lessons, and supportive feedback so students can progress with confidence.\n\n"
            ."Learners can expect an approachable teaching style, relevant industry context, and guidance that connects concepts to outcomes."
            .$note;

        return [
            'bio' => $bio,
            'highlight_points' => [
                'Clear, learner-friendly explanations',
                'Practical '.$profession.' perspective',
                'Hands-on examples and practice focus',
                'Supportive doubt-clearing style',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function offlineSubAdminDetailsFallback(string $prompt, ?string $reason = null, ?string $emailDomain = null): array
    {
        preg_match('/Sub-admin name:\s*(.+)/i', $prompt, $nm);
        preg_match('/Designation\s*(?:\/ role title)?:\s*(.+)/i', $prompt, $dm);
        $name = trim($nm[1] ?? 'This team member');
        $name = preg_replace('/\s+/', ' ', $name) ?: 'This team member';
        $designation = trim($dm[1] ?? 'Sub Admin');
        $designation = preg_replace('/\s+/', ' ', $designation) ?: 'Sub Admin';

        $note = $reason
            ? "\n\n(AI draft note: {$reason})"
            : "\n\n(AI draft — configure API key in AI Center for richer generation.)";

        $bio = "{$name} serves as {$designation} for the institute, helping keep day-to-day LMS operations organized and learner-friendly.\n\n"
            ."In the {$designation} capacity, {$name} supports course and learner workflows, coordinates with instructors, and ensures assigned scopes are handled promptly.\n\n"
            ."Clear communication, careful follow-up, and a practical approach to admin tasks are the focus of this role."
            .$note;

        return [
            'bio' => $bio,
            'suggested_email' => $this->suggestStaffEmail($name, $emailDomain),
            'responsibility_points' => [
                'Manage assigned courses / scopes in the LMS',
                'Coordinate with instructors and support staff',
                'Monitor learner requests and escalate when needed',
                'Keep records and status updates accurate',
                'Follow institute processes for '.$designation.' duties',
            ],
        ];
    }

    protected function offlineFallback(string $system, string $prompt, ?string $reason = null): string
    {
        $note = $reason ? "\n\n(Provider note: {$reason})" : "\n\n(Offline draft — configure AI API key in AI Center settings.)";

        return "## Draft output\n\nBased on your request:\n\n{$prompt}\n\nSuggested structure:\n1. Introduction\n2. Core concepts\n3. Practice tasks\n4. Summary\n{$note}";
    }
}

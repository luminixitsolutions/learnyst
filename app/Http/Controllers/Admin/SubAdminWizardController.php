<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\Role;
use App\Models\SubAdminScope;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class SubAdminWizardController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AiService $ai) {}

    protected function sessionKey(): string
    {
        return 'sub_admin_wizard';
    }

    public function create()
    {
        Session::forget($this->sessionKey());

        return redirect()->route('admin.sub-admins.wizard.step', 1);
    }

    public function aiAnalyze(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:2000'],
        ]);

        $emailDomain = Str::after((string) Auth::user()?->email, '@') ?: null;

        try {
            $details = $this->ai->generateSubAdminDetails(
                Auth::user(),
                $validated['name'],
                $validated['designation'],
                $validated['brief'] ?? null,
                $emailDomain
            );
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->errors()['ai'][0] ?? 'AI request failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        \App\Models\AiGeneration::create([
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
            'feature' => 'sub_admin_details',
            'title' => $validated['name'].' — '.$validated['designation'],
            'prompt' => 'Name: '.$validated['name']
                ."\nDesignation: ".$validated['designation']
                .(! empty($validated['brief']) ? "\nBrief: ".$validated['brief'] : ''),
            'output' => json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'status' => 'draft',
            'meta' => ['source' => 'sub_admin_wizard_step1'],
        ]);

        ActivityLogger::log('ai_sub_admin_details', 'AI filled sub-admin details for: '.$validated['name']);

        return response()->json([
            'ok' => true,
            'message' => 'Profile notes generated from designation. Review and continue.',
            'data' => $details,
        ]);
    }

    public function step(int $step)
    {
        $data = Session::get($this->sessionKey(), []);

        return match ($step) {
            1 => view('admin.sub-admins.wizard.step1', compact('data')),
            2 => view('admin.sub-admins.wizard.step2', [
                'data' => $data,
                'roles' => Role::where('is_system', false)->orWhere('slug', 'sub-admin')->get(),
            ]),
            3 => view('admin.sub-admins.wizard.step3', [
                'data' => $data,
                'courses' => $this->owned(Course::query())->orderBy('title')->get(),
            ]),
            4 => view('admin.sub-admins.wizard.step4', [
                'data' => $data,
                'bundles' => $this->owned(Bundle::query())->orderBy('title')->get(),
            ]),
            5 => view('admin.sub-admins.wizard.step5', [
                'data' => $data,
                'communities' => $this->owned(Community::query())->orderBy('name')->get(),
            ]),
            6 => view('admin.sub-admins.wizard.step6', compact('data')),
            default => redirect()->route('admin.sub-admins.wizard.step', 1),
        };
    }

    public function storeStep(Request $request, int $step)
    {
        $data = Session::get($this->sessionKey(), []);

        match ($step) {
            1 => $data['details'] = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'phone' => ['nullable', 'string'],
                'password' => ['required', Password::defaults()],
                'avatar' => ['nullable', 'image', 'max:2048'],
                'expertise' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string'],
                'social_links' => ['nullable', 'array'],
            ]),
            2 => $data['role_id'] = $request->validate(['role_id' => ['required', 'exists:roles,id']])['role_id'],
            3 => $data['course_ids'] = $request->validate([
                'course_ids' => ['nullable', 'array'],
                'course_ids.*' => [Rule::in($this->ownedCourseIds())],
            ])['course_ids'] ?? [],
            4 => $data['bundle_ids'] = $request->validate([
                'bundle_ids' => ['nullable', 'array'],
                'bundle_ids.*' => [Rule::in($this->ownedBundleIds())],
            ])['bundle_ids'] ?? [],
            5 => $data['community_ids'] = $request->validate([
                'community_ids' => ['nullable', 'array'],
                'community_ids.*' => [Rule::in($this->owned(Community::query())->pluck('id'))],
            ])['community_ids'] ?? [],
            default => null,
        };

        if ($step === 1 && $request->hasFile('avatar')) {
            $data['details']['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        Session::put($this->sessionKey(), $data);

        if ($step >= 6) {
            return redirect()->route('admin.sub-admins.wizard.step', 6);
        }

        return redirect()->route('admin.sub-admins.wizard.step', $step + 1);
    }

    public function finish(Request $request)
    {
        $data = Session::get($this->sessionKey(), []);

        if (empty($data['details']) || empty($data['role_id'])) {
            return redirect()->route('admin.sub-admins.wizard.step', 1)->with('error', 'Please complete all required steps.');
        }

        $roleId = $data['role_id'];
        if (!Role::where('id', $roleId)->whereIn('slug', ['sub-admin'])->orWhere('is_system', false)->exists()) {
            $roleId = Role::where('slug', 'sub-admin')->first()?->id ?? $roleId;
        }

        $user = User::create([
            'name' => $data['details']['name'],
            'email' => $data['details']['email'],
            'phone' => $data['details']['phone'] ?? null,
            'password' => Hash::make($data['details']['password']),
            'avatar' => $data['details']['avatar_path'] ?? null,
            'expertise' => $data['details']['expertise'] ?? null,
            'bio' => $data['details']['bio'] ?? null,
            'social_links' => $data['details']['social_links'] ?? [],
            'role_id' => $roleId,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => Auth::id(),
        ]);

        foreach ($data['course_ids'] ?? [] as $courseId) {
            SubAdminScope::create(['user_id' => $user->id, 'scope_type' => Course::class, 'scope_id' => $courseId]);
        }
        foreach ($data['bundle_ids'] ?? [] as $bundleId) {
            SubAdminScope::create(['user_id' => $user->id, 'scope_type' => Bundle::class, 'scope_id' => $bundleId]);
        }
        foreach ($data['community_ids'] ?? [] as $communityId) {
            SubAdminScope::create(['user_id' => $user->id, 'scope_type' => Community::class, 'scope_id' => $communityId]);
        }

        ActivityLogger::log('sub_admin_created', "Sub-admin {$user->name} created via wizard", $user);
        Session::forget($this->sessionKey());

        return redirect()->route('admin.sub-admins.show', $user)->with('success', 'Sub-admin created successfully.');
    }
}

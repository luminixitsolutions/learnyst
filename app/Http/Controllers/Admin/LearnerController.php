<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LearnerController extends Controller
{
    public function index(Request $request)
    {
        $learnerRole = Role::where('slug', 'learner')->first();

        $query = User::where('role_id', $learnerRole?->id)->withCount('enrollments')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $learners = $query->paginate(20)->withQueryString();

        return view('admin.learners.index', compact('learners'));
    }

    public function create()
    {
        return view('admin.learners.create');
    }

    public function store(Request $request)
    {
        $learnerRole = Role::where('slug', 'learner')->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'password' => ['required', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'email_verified' => ['boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $validated['role_id'] = $learnerRole->id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['email_verified_at'] = $request->boolean('email_verified', true) ? now() : null;
        unset($validated['email_verified']);

        $learner = User::create($validated);
        ActivityLogger::log('learner_created', "Learner {$learner->name} created", $learner);

        return redirect()->route('admin.learners.show', $learner)->with('success', 'Learner created.');
    }

    public function show(User $learner)
    {
        $learner->load(['enrollments.course', 'orders.items.course', 'certificates']);
        $courses = Course::where('status', 'published')->orderBy('title')->get();
        $learners = User::whereHas('role', fn ($q) => $q->where('slug', 'learner'))->where('id', '!=', $learner->id)->orderBy('name')->get();

        return view('admin.learners.show', compact('learner', 'courses', 'learners'));
    }

    public function edit(User $learner)
    {
        return view('admin.learners.edit', compact('learner'));
    }

    public function update(Request $request, User $learner)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $learner->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'password' => ['nullable', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'email_verified' => ['boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['email_verified_at'] = $request->boolean('email_verified', (bool) $learner->email_verified_at) ? ($learner->email_verified_at ?? now()) : null;
        unset($validated['email_verified']);
        $learner->update($validated);

        ActivityLogger::log('learner_updated', "Learner {$learner->name} updated", $learner);

        return redirect()->route('admin.learners.show', $learner)->with('success', 'Learner updated.');
    }

    public function destroy(User $learner)
    {
        ActivityLogger::log('learner_deleted', "Learner {$learner->name} deleted", $learner);
        $learner->delete();

        return redirect()->route('admin.learners.index')->with('success', 'Learner deleted.');
    }

    public function enroll(Request $request, User $learner)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'access_type' => ['required', 'in:free,trial,paid'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'show_custom_fields' => ['boolean'],
        ]);

        CourseEnrollment::updateOrCreate(
            ['user_id' => $learner->id, 'course_id' => $validated['course_id'], 'enrollment_type' => 'course'],
            [
                'status' => 'active',
                'access_type' => $validated['access_type'],
                'amount' => $validated['amount'] ?? null,
                'show_custom_fields' => $request->boolean('show_custom_fields'),
                'enrolled_at' => now(),
                'access_starts_at' => now(),
                'expires_at' => $validated['expires_at'] ?? null,
            ]
        );

        ActivityLogger::log('learner_enrolled', "Course access granted to {$learner->name}", $learner);

        return back()->with('success', 'Course access granted.');
    }

    public function revokeEnrollment(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'revoked']);

        return back()->with('success', 'Course access revoked.');
    }

    public function export()
    {
        $learnerRole = Role::where('slug', 'learner')->first();
        $learners = User::where('role_id', $learnerRole?->id)->get();

        $filename = 'learners_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($learners) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Phone', 'Total Spent', 'Last Login', 'Signup Date']);
            foreach ($learners as $learner) {
                fputcsv($file, [
                    $learner->name,
                    $learner->email,
                    $learner->phone,
                    $learner->total_spent,
                    $learner->last_login_at?->format('Y-m-d H:i'),
                    $learner->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $learnerRole = Role::where('slug', 'learner')->firstOrFail();
        $imported = 0;

        if (($handle = fopen($request->file('file')->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) continue;
                User::firstOrCreate(['email' => trim($row[1])], [
                    'name' => trim($row[0]),
                    'role_id' => $learnerRole->id,
                    'phone' => $row[2] ?? null,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
                $imported++;
            }
            fclose($handle);
        }

        return back()->with('success', "{$imported} learners imported.");
    }
}

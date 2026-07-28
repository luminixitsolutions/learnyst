<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $branches = $this->owned(Branch::query())
            ->withCount(['users', 'orders'])
            ->with('admins')
            ->latest()
            ->get();

        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'revenue_share_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        $validated['revenue_share_percent'] = $validated['revenue_share_percent'] ?? 0;
        $branch = Branch::create($validated);
        ActivityLogger::log('branch_created', "Branch {$branch->name} created", $branch);

        return back()->with('success', 'Branch created under this company (tenant unchanged).');
    }

    public function show(Branch $branch)
    {
        $this->authorizeOwner($branch);
        $branch->load(['admins', 'users']);
        $staff = $this->ownedUsersQuery()->orderBy('name')->get(['id', 'name', 'email']);
        $learners = $this->visibleLearnersQuery()->orderBy('name')->limit(200)->get(['id', 'name', 'email']);

        $revenue = Order::where('branch_id', $branch->id)->where('payment_status', 'paid')->sum('total');
        $branchShare = $revenue * ((float) $branch->revenue_share_percent / 100);
        $hqShare = $revenue - $branchShare;

        return view('admin.branches.show', compact('branch', 'staff', 'learners', 'revenue', 'branchShare', 'hqShare'));
    }

    public function assignAdmin(Request $request, Branch $branch)
    {
        $this->authorizeOwner($branch);
        $validated = $request->validate([
            'user_id' => ['required', Rule::in($this->ownedUsersQuery()->pluck('id')->push(Auth::id())->all())],
        ]);
        $branch->admins()->syncWithoutDetaching([$validated['user_id']]);

        return back()->with('success', 'Branch admin assigned.');
    }

    public function assignUser(Request $request, Branch $branch)
    {
        $this->authorizeOwner($branch);
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'role_in_branch' => ['required', 'in:learner,staff'],
        ]);

        $allowed = $this->visibleLearnersQuery()->whereKey($validated['user_id'])->exists()
            || $this->ownedUsersQuery()->whereKey($validated['user_id'])->exists();
        abort_unless($allowed, 403);

        $branch->users()->syncWithoutDetaching([
            $validated['user_id'] => ['role_in_branch' => $validated['role_in_branch']],
        ]);

        return back()->with('success', 'User allocated to branch.');
    }

    public function reports()
    {
        $branches = $this->owned(Branch::query())->where('is_active', true)->get();
        $rows = $branches->map(function (Branch $b) {
            $revenue = (float) Order::where('branch_id', $b->id)->where('payment_status', 'paid')->sum('total');
            $share = $revenue * ((float) $b->revenue_share_percent / 100);

            return [
                'branch' => $b,
                'learners' => $b->users()->wherePivot('role_in_branch', 'learner')->count(),
                'revenue' => $revenue,
                'branch_share' => $share,
                'hq_share' => $revenue - $share,
            ];
        });

        return view('admin.branches.reports', compact('rows'));
    }

    public function updateShare(Request $request, Branch $branch)
    {
        $this->authorizeOwner($branch);
        $validated = $request->validate([
            'revenue_share_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $branch->update($validated);

        return back()->with('success', 'Revenue share updated.');
    }
}

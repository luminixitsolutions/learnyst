@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Role')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 2 — Role')

@section('content')
<div class="max-w-2xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 2])

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.sub-admins.wizard.store', 2) }}" class="space-y-5">
            @csrf
            <x-form-input label="Assign Role" name="role_id" type="select" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $data['role_id'] ?? '') == $role->id)>{{ $role->name }} @if($role->is_system)(System)@endif</option>
                @endforeach
            </x-form-input>
            <p class="text-xs text-slate-500">The role determines which permissions this sub-admin has. You can customize permissions in Roles & Permissions.</p>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.wizard.step', 1) }}" class="text-sm text-slate-500 hover:text-white">← Back</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Next: Courses →</button>
            </div>
        </form>
    </div>
</div>
@endsection

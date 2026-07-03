@extends('layouts.app')

@section('title', 'Create Course')
@section('page-title', 'Create Course')
@section('breadcrumb', 'Products / New Course')

@section('content')
<div class="max-w-4xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.courses._form')
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded-lg text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Create Course</button>
            </div>
        </form>
    </div>
</div>
@endsection

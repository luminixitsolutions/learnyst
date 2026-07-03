@extends('layouts.app')

@section('title', 'Edit Homepage Section')
@section('page-title', 'Edit Homepage Section')
@section('breadcrumb', 'Website / Edit Section')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.website-sections.update', $websiteSection) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            @include('admin.website-sections._form', ['websiteSection' => $websiteSection])
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.website-sections.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Update Section</button>
            </div>
        </form>
    </div>
</div>
@endsection

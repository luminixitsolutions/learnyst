@extends('layouts.app')

@section('title', 'Add Homepage Section')
@section('page-title', 'Add Homepage Section')
@section('breadcrumb', 'Website / Create Section')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.website-sections.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.website-sections._form')
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.website-sections.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Section</button>
            </div>
        </form>
    </div>
</div>
@endsection

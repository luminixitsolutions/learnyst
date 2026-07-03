@extends('layouts.app')

@section('title', $module['title'])
@section('page-title', $module['title'])
@section('breadcrumb', 'Products')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <x-empty-state
        :title="$module['title']"
        :description="$module['description'] . ' This module will be available in a future update.'"
    />
</div>
@endsection

@extends('layouts.app')

@section('title', 'Insights Dashboard')
@section('page-title', 'Insights Dashboard')
@section('breadcrumb', 'Insights / Dashboard')

@section('content')
<div class="space-y-6">
    @include('admin.insights.partials.vitals-cards')
</div>
@endsection

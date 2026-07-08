@extends('layouts.app')

@section('title', 'School Vitals')
@section('page-title', 'School Vitals')
@section('breadcrumb', 'Insights / School Vitals')

@section('content')
<div class="space-y-6">
    @include('admin.insights.partials.vitals-cards')
</div>
@endsection

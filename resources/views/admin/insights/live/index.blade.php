@extends('layouts.app')

@section('title', 'Live Dashboard')
@section('page-title', 'Live Dashboard')
@section('breadcrumb', 'Insights / Live Dashboard')

@section('content')
@include('admin.insights.partials.hub-cards', [
    'backRoute' => route('admin.insights.dashboard'),
    'cards' => [
        ['Live Classes', 'Class sessions and engagement', 'admin.insights.live.classes'],
        ['Checkout', 'Conversion and abandonment analytics', 'admin.insights.live.checkout'],
        ['Test Takes', 'Test attempt analytics', 'admin.insights.live.test-takes'],
    ],
])
@endsection

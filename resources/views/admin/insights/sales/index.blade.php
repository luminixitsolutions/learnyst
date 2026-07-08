@extends('layouts.app')

@section('title', 'Sales Insight')
@section('page-title', 'Sales Insight')
@section('breadcrumb', 'Insights / Sales')

@section('content')
@include('admin.insights.partials.hub-cards', [
    'backRoute' => route('admin.insights.dashboard'),
    'cards' => [
        ['Fresh Trial Insights', 'New trial enrollments and activity', 'admin.insights.sales.fresh-trial'],
        ['Upsell Trial Insights', 'Trials converted to paid', 'admin.insights.sales.upsell-trial'],
        ['Renewal Trial Insights', 'Expiring and renewal status', 'admin.insights.sales.renewal-trial'],
        ['Free Users Insights', 'Free enrollments and upsell potential', 'admin.insights.sales.free-users'],
    ],
])
@endsection

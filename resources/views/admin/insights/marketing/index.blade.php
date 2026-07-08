@extends('layouts.app')

@section('title', 'Marketing Insight')
@section('page-title', 'Marketing Insight')
@section('breadcrumb', 'Insights / Marketing')

@section('content')
@include('admin.insights.partials.hub-cards', [
    'backRoute' => route('admin.insights.dashboard'),
    'cards' => [
        ['CTA Insights', 'Click-through and conversion by CTA', 'admin.insights.marketing.cta'],
    ],
])
@endsection

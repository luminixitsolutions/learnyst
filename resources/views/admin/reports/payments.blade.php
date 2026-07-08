{{-- Legacy alias: controller renders transactions view directly --}}
@extends('layouts.app')
@section('title', 'Payments Report')
@section('page-title', 'Payments Report')
@section('breadcrumb', 'Reports / Payments')
@section('content')
@if(isset($payments))
@include('admin.reports.partials.transactions-table')
@endif
@endsection

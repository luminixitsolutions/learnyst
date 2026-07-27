@extends('layouts.app')

@section('title', 'Certificates')
@section('page-title', 'My Certificates')
@section('breadcrumb', 'Alumni / Certificates')

@section('content')
@php
    $renewRoute = fn ($cert) => route('alumni.certificates.renew', $cert);
    $downloadRoute = fn ($cert) => route('learner.certificates.download', $cert);
@endphp
@include('partials.certificates-table', compact('certificates', 'renewRoute', 'downloadRoute'))
@endsection

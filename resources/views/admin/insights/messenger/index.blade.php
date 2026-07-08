@extends('layouts.app')

@section('title', 'Messenger Insight')
@section('page-title', 'Messenger Insight')
@section('breadcrumb', 'Insights / Messenger')

@section('content')
@include('admin.insights.partials.hub-cards', [
    'backRoute' => route('admin.insights.dashboard'),
    'cards' => [
        ['System Mails Report', 'Transactional system emails', 'admin.insights.messenger.system-mails'],
        ['Marketing Mails Report', 'Marketing email campaigns', 'admin.insights.messenger.marketing-mails'],
        ['Push Messages Report', 'Push notification delivery', 'admin.insights.messenger.push-messages'],
        ['Workflow Mails Report', 'Automated workflow emails', 'admin.insights.messenger.workflow-mails'],
        ['Email Delivery Report', 'Per-recipient delivery stats', 'admin.insights.messenger.email-delivery'],
        ['Bounces & Complaints Report', 'Bounce and complaint tracking', 'admin.insights.messenger.bounces-complaints'],
        ['WhatsApp Messages Report', 'WhatsApp campaign messages', 'admin.insights.messenger.whatsapp-messages'],
        ['WhatsApp Workflow Messages', 'WhatsApp workflow automation', 'admin.insights.messenger.whatsapp-workflow'],
    ],
])
@endsection

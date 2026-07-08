@extends('layouts.app')

@section('title', 'Sales Reports')
@section('page-title', 'Sales Reports')
@section('breadcrumb', 'Reports / Sales')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-indigo-600">← All Reports</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['Product Sales', 'Orders by product with discounts', 'admin.reports.product-sales'],
            ['Referral & Wallet', 'Referral codes and wallet transactions', 'admin.reports.referral-wallet'],
            ['Affiliate Products', 'Product-wise affiliate performance', 'admin.reports.affiliate-products'],
            ['Affiliate Report', 'Affiliate commissions and payouts', 'admin.reports.affiliates'],
            ['Coupons', 'Coupon usage and discount totals', 'admin.reports.coupons'],
            ['Broadcast Messages', 'Email, WhatsApp and SMS campaigns', 'admin.reports.broadcast'],
            ['Order Sales', 'Paid orders and revenue summary', 'admin.reports.sales'],
        ] as [$title, $desc, $route])
        <a href="{{ route($route) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition group">
            <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600">{{ $title }}</h3>
            <p class="text-sm text-slate-500 mt-2">{{ $desc }}</p>
            <span class="inline-block mt-4 text-sm text-indigo-600">View report →</span>
        </a>
        @endforeach
    </div>
</div>
@endsection

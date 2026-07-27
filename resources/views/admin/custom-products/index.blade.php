@extends('layouts.app')

@section('title', 'Custom Products')
@section('page-title', 'Custom Products')
@section('breadcrumb', 'More Products / Custom Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.more-products.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage custom digital products and services.</p>
        <a href="{{ route('admin.custom-products.create') }}" class="panel-btn-primary">Create Product</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($customProducts->count())
        <div class="overflow-x-auto">
            <table id="customProductsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Security</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customProducts as $product)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $product->title }}</td>
                        <td class="px-6 py-4">{{ $product->is_free ? 'Free' : '₹'.number_format($product->price, 0) }}</td>
                        <td class="px-6 py-4">{{ $product->contentSecurityLabel() }}</td>
                        <td class="px-6 py-4">{{ ucfirst($product->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $product->created_at->timestamp }}">{{ $product->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions :delete-url="route('admin.custom-products.destroy', $product)" delete-title="Delete product" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No custom products yet" :action="route('admin.custom-products.create')" actionLabel="Create Product" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($customProducts->count())
    <x-admin.datatable-scripts table-id="customProductsTable" entity="custom products" :order-column="4" order-direction="desc" :action-column="5" export-file-name="custom-products" />
@endif
@endpush

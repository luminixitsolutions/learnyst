<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MoreProductsController extends Controller
{
    public function index()
    {
        $products = collect(config('more-products', []))->map(function ($product) {
            $product['url'] = route($product['route']);

            return $product;
        });

        return view('admin.more-products.index', compact('products'));
    }
}

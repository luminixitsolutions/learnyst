<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomProduct;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomProductController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomProduct::with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'published_date');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            'price' => $query->orderBy('price'),
            default => $query->latest(),
        };

        $customProducts = $query->paginate(15)->withQueryString();

        return view('admin.custom-products.index', compact('customProducts', 'sort'));
    }

    public function create()
    {
        return view('admin.custom-products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'content_security' => ['required', Rule::in(['encryption', 'no_encryption'])],
        ]);

        $isFree = $request->boolean('is_free');

        if ($isFree) {
            $validated['price'] = 0;
            $validated['is_free'] = true;
        } else {
            $validated['is_free'] = false;
            $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
            ]);
            $validated['price'] = $request->input('price');
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $product = CustomProduct::create($validated);

        ActivityLogger::log('custom_product_created', "Custom product {$product->title} created", $product);

        return redirect()
            ->route('admin.custom-products.index')
            ->with('success', 'Digital product created successfully.');
    }

    public function destroy(CustomProduct $customProduct)
    {
        $title = $customProduct->title;
        $customProduct->delete();

        ActivityLogger::log('custom_product_deleted', "Custom product {$title} deleted");

        return redirect()
            ->route('admin.custom-products.index')
            ->with('success', 'Digital product deleted.');
    }
}

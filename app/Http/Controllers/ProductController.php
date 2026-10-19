<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\ProductDTO;
use App\Http\Requests\StoreProductRequest;
use App\Http\Responses\ProductFormResponse;
use App\Models\Product;
use App\Pipelines\Filters\Product\ProductFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status', 'all');

        $queryBuilder =  Product::withSum('soldItems', 'quantity')->orderBy('name')->latest()->orderBy('name');

        $products = resolve(ProductFilter::class)->getResults([
            'builder' => $queryBuilder,
            'params' => $request->query()
        ]);

        return view('products.index', compact('products', 'search', 'status'));
    }

    public function create(): ProductFormResponse
    {
        return new ProductFormResponse();
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create(ProductDTO::fromRequest($request, defaultActive: true)->toArray());

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): ProductFormResponse
    {
        return new ProductFormResponse($product);
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $product->update(ProductDTO::fromRequest($request, defaultActive: false)->toArray());

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->loadSum('soldItems', 'quantity');

        if ((int) ($product->sold_items_sum_quantity ?? 0) > 0) {
            return back()->with('error', 'Cannot delete a product that appears on a completed invoice.');
        }

        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Cannot delete a product that has been ordered.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}

@extends('layouts.app')
@section('title', 'Products')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Products</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            New product
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-4">
            <div class="flex gap-1 rounded-lg bg-slate-100 p-1 text-sm font-semibold">
                @foreach (['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                    <a href="{{ route('products.index', array_filter(['status' => $value === 'all' ? null : $value, 'q' => $search])) }}"
                       class="rounded-md px-3 py-1.5 transition {{ $status === $value ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">{{ $label }}</a>
                @endforeach
            </div>
            <form method="GET" class="flex gap-2">
                @if ($status !== 'all') <input type="hidden" name="status" value="{{ $status }}"> @endif
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name or SKU" class="input w-64">
                <button class="btn btn-secondary">Search</button>
                @if ($search) <a href="{{ route('products.index', ['status' => $status !== 'all' ? $status : null]) }}" class="btn btn-secondary">Clear</a> @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">SKU</th>
                        <th class="th">Name</th>
                        <th class="th text-right">Price</th>
                        <th class="th text-right">Stock</th>
                        <th class="th text-right">Total sold</th>
                        <th class="th">Status</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-sky-50/50">
                            <td class="td font-mono text-xs text-slate-500">{{ $product->sku }}</td>
                            <td class="td font-semibold text-slate-900">{{ $product->name }}</td>
                            <td class="td num">{{ money($product->price) }}</td>
                            <td class="td num {{ $product->stock_quantity === 0 ? 'font-bold text-rose-600' : ($product->stock_quantity <= 5 ? 'font-semibold text-amber-600' : '') }}">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="td num">{{ (int) ($product->sold_items_sum_quantity ?? 0) }}</td>
                            <td class="td">
                                @if ($product->is_active)
                                    <span class="badge ring-emerald-600/20 bg-emerald-50 text-emerald-700">Active</span>
                                @else
                                    <span class="badge ring-slate-400/20 bg-slate-100 text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('products.edit', $product) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          data-confirm="Delete {{ $product->name }}? This cannot be undone.">
                                        @csrf @method('DELETE')
                                        <button
                                            @disabled($product->sold_items_sum_quantity == 0)
                                            class="text-sm font-semibold {{ $product->sold_items_sum_quantity == 0
                                                ? 'text-gray-600 hover:text-gray-700'
                                                : 'text-rose-600 hover:text-rose-700 cursor-pointer' }}"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">No products found. <a href="{{ route('products.create') }}" class="font-semibold text-blue-600">Add the first one.</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $products->links() }}</div>
        @endif
    </div>
@endsection

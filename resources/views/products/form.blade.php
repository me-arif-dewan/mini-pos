@extends('layouts.app')
@section('title', $product ? 'Edit '.$product->name : 'New product')

@section('content')
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">&larr; All products</a>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $product ? 'Edit '.$product->name : 'New product' }}</h1>
    </div>

    <div class="card mx-auto max-w-xl p-6">
        <form method="POST" action="{{ $product ? route('products.update', $product) : route('products.store') }}">
            @csrf
            @if ($product) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label for="sku" class="label">SKU <span class="text-rose-500">*</span></label>
                    <input id="sku" type="text" name="sku" value="{{ old('sku', $product?->sku) }}"
                           class="input @error('sku') input-error @enderror" required>
                    @error('sku') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="label">Name <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name', $product?->name) }}"
                           class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="price" class="label">Price <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-400">{{ config('pos.currency_symbol') }}</span>
                            <input id="price" type="number" name="price" min="0" step="0.01"
                                   value="{{ old('price', $product ? (float) $product->price : '') }}"
                                   class="input pl-7 @error('price') input-error @enderror" required>
                        </div>
                        @error('price') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="label">Stock quantity <span class="text-rose-500">*</span></label>
                        <input id="stock_quantity" type="number" name="stock_quantity" min="0" step="1"
                               value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}"
                               class="input @error('stock_quantity') input-error @enderror" required>
                        @error('stock_quantity') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input id="is_active" type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $product ? $product->is_active : true))
                           class="size-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">Active (visible in order form)</label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="btn btn-primary">{{ $product ? 'Save changes' : 'Create product' }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')
@section('title', isset($order) ? "Edit {$order->order_number}" : 'New order')

@php
    $isEdit   = isset($order) && $order !== null;
    $formAction = $isEdit ? route('orders.update', $order) : route('orders.store');
    $oldItems = old('items', $isEdit ? $order->items->map(fn($i) => [
        'product_id'     => $i->product_id,
        'quantity'       => $i->quantity,
        'discount_type'  => $i->discount_type?->value ?? 'percent',
        'discount_value' => (float) $i->discount_value,
    ])->values()->all() : []);
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEdit ? "Edit {$order->order_number}" : 'New sales order' }}</h1>
        <p class="mt-1 text-sm text-slate-500">Orders are saved as pending. Stock is only deducted when you complete the order.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <p class="font-bold">Please fix the highlighted problems.</p>
            @if ($errors->has('items') || $errors->has('items.*'))
                <p class="mt-1">{{ $errors->first('items') }}</p>
            @endif
        </div>
    @endif

    <form id="order-form" method="POST" action="{{ $formAction }}" novalidate
          data-tax-rate="{{ config('pos.tax_rate') }}"
          data-currency="{{ config('pos.currency_symbol') }}"
          data-old-items='@json($oldItems)'
          data-errors='@json($errors->getMessages())'>
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- Customer --}}
                <section class="card p-6">
                    <h2 class="card-title">Customer</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="customer_id" class="label">Customer</label>
                            <select id="customer_id" name="customer_id" class="input @error('customer_id') input-error @enderror">
                                <option value="">Choose a customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            data-email="{{ $customer->email }}" data-phone="{{ $customer->phone }}" data-address="{{ $customer->address }}"
                                            @selected(old('customer_id', $isEdit ? $order->customer_id : '') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="order_date" class="label">Order date</label>
                            <input id="order_date" type="date" name="order_date"
                                   value="{{ old('order_date', $isEdit ? $order->order_date->toDateString() : now()->toDateString()) }}"
                                   class="input @error('order_date') input-error @enderror">
                            @error('order_date') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <dl id="customer-info" class="mt-4 hidden rounded-xl bg-sky-50 px-4 py-3 text-sm text-slate-600 ring-1 ring-sky-100">
                        <div class="flex flex-wrap gap-x-6 gap-y-1">
                            <div><dt class="inline font-semibold text-slate-700">Email: </dt><dd id="ci-email" class="inline"></dd></div>
                            <div><dt class="inline font-semibold text-slate-700">Phone: </dt><dd id="ci-phone" class="inline"></dd></div>
                        </div>
                        <div class="mt-1"><dt class="inline font-semibold text-slate-700">Address: </dt><dd id="ci-address" class="inline"></dd></div>
                    </dl>
                </section>

                {{-- Items --}}
                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="card-title">Items</h2>
                        <button type="button" id="add-item" class="btn btn-secondary btn-sm">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                            Add item
                        </button>
                    </div>

                    <div id="item-rows" class="mt-4 space-y-3"></div>

                    <p id="empty-items" class="mt-4 hidden rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No items yet. Select <span class="font-semibold">Add item</span> to start the order.
                    </p>
                </section>

                <section class="card p-6">
                    <label for="notes" class="label">Notes (shown on the invoice)</label>
                    <textarea id="notes" name="notes" rows="3" maxlength="1000" class="input">{{ old('notes', $isEdit ? $order->notes : '') }}</textarea>
                </section>
            </div>

            {{-- Summary --}}
            <aside class="lg:col-span-1">
                <div class="card sticky top-6 overflow-hidden">
                    <div class="bg-linear-to-r from-blue-600 to-cyan-500 px-6 py-4 text-white">
                        <h2 class="text-base font-bold">Order summary</h2>
                    </div>

                    <dl class="space-y-3 p-6 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd id="sum-subtotal" class="font-semibold tabular-nums">{{ money(0) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Item discounts</dt><dd id="sum-line-discounts" class="font-semibold tabular-nums text-rose-600">-{{ money(0) }}</dd></div>

                        <div class="rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-500">Order discount</dt>
                                <dd id="sum-order-discount" class="font-semibold tabular-nums text-rose-600">-{{ money(0) }}</dd>
                            </div>
                            <div class="mt-2 flex gap-2">
                                <select id="order-discount-type" name="discount_type" class="input w-24 shrink-0">
                                    <option value="percent" @selected(old('discount_type', $isEdit ? $order->discount_type?->value : 'percent') === 'percent')>%</option>
                                    <option value="fixed" @selected(old('discount_type', $isEdit ? $order->discount_type?->value : '') === 'fixed')>{{ config('pos.currency_symbol') }}</option>
                                </select>
                                <input id="order-discount-value" name="discount_value" type="number" min="0" step="0.01" inputmode="decimal"
                                       value="{{ old('discount_value', $isEdit ? (float) $order->discount_value : 0) }}"
                                       class="input @error('discount_value') input-error @enderror">
                            </div>
                            @error('discount_value') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-between border-t border-slate-200 pt-3"><dt class="text-slate-500">Taxable amount</dt><dd id="sum-taxable" class="font-semibold tabular-nums">{{ money(0) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Tax ({{ rtrim(rtrim(number_format(config('pos.tax_rate'), 2), '0'), '.') }}%)</dt><dd id="sum-tax" class="font-semibold tabular-nums text-amber-600">{{ money(0) }}</dd></div>
                        <div class="flex items-end justify-between border-t border-slate-200 pt-4">
                            <dt class="font-bold text-slate-900">Grand total</dt>
                            <dd id="sum-grand" class="text-2xl font-extrabold tabular-nums text-blue-700">{{ money(0) }}</dd>
                        </div>
                    </dl>

                    <div class="space-y-2 border-t border-slate-100 bg-slate-50/60 p-6">
                        <button id="submit-order" type="submit" class="btn btn-primary w-full" {{ $isEdit ? '' : 'disabled' }}>
                            {{ $isEdit ? 'Save changes' : 'Save as pending' }}
                        </button>
                        <a href="{{ $isEdit ? route('orders.show', $order) : route('orders.index') }}" class="btn btn-secondary w-full">Cancel</a>
                    </div>
                </div>
            </aside>
        </div>

        {{-- One line item. __INDEX__ is replaced in order-form.js --}}
        <template id="item-row-template">
            <div class="js-row rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                <div class="grid gap-3 sm:grid-cols-12">
                    <div class="sm:col-span-12 xl:col-span-5">
                        <label class="label">Product</label>
                        <select name="items[__INDEX__][product_id]" class="js-product input">
                            <option value="">Choose a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                        <p class="js-stock mt-1 text-xs font-medium text-slate-400">Pick a product</p>
                    </div>
                    <div class="sm:col-span-4 xl:col-span-2">
                        <label class="label">Unit price</label>
                        <input type="text" readonly tabindex="-1" class="js-price input" placeholder="0.00">
                    </div>
                    <div class="sm:col-span-3 xl:col-span-1">
                        <label class="label">Qty</label>
                        <input type="number" min="1" step="1" value="1" name="items[__INDEX__][quantity]" class="js-qty input">
                    </div>
                    <div class="sm:col-span-5 xl:col-span-4">
                        <label class="label">Discount</label>
                        <div class="flex gap-2">
                            <select name="items[__INDEX__][discount_type]" class="js-dtype input w-20 shrink-0">
                                <option value="percent">%</option>
                                <option value="fixed">{{ config('pos.currency_symbol') }}</option>
                            </select>
                            <input type="number" min="0" step="0.01" value="0" inputmode="decimal" name="items[__INDEX__][discount_value]" class="js-dvalue input">
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <button type="button" class="js-remove text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                    <p class="text-sm text-slate-500">Line total <span class="js-line-total ml-1 text-base font-bold tabular-nums text-slate-900">{{ money(0) }}</span></p>
                </div>
                <div class="js-row-errors"></div>
            </div>
        </template>
    </form>
@endsection

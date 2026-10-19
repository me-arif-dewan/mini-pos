@extends('layouts.app')
@section('title', $order->order_number)

@section('content')
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">&larr; All orders</a>
            <div class="mt-2 flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $order->order_number }}</h1>
                <span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Placed {{ $order->order_date->format('d M Y') }}
                @if ($order->completed_at) &middot; completed {{ $order->completed_at->format('d M Y, H:i') }} @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('orders.invoice', $order) }}" class="btn btn-secondary">
                {{ $order->isCompleted() ? 'View invoice' : 'Preview invoice' }}
            </a>
            @if ($order->isPending())
                <a href="{{ route('orders.edit', $order) }}" class="btn btn-secondary">Edit order</a>
                <form method="POST" action="{{ route('orders.cancel', $order) }}" data-confirm="Cancel {{ $order->order_number }}? This can't be undone.">
                    @csrf
                    <button class="btn btn-danger">Cancel order</button>
                </form>
                <form method="POST" action="{{ route('orders.complete', $order) }}" data-confirm="Complete {{ $order->order_number }}? Stock will be deducted and the sale posted to the ledger.">
                    @csrf
                    <button class="btn btn-success">Complete order</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-4"><h2 class="card-title">Items</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="th">Product</th>
                                <th class="th text-right">Price</th>
                                <th class="th text-right">Qty</th>
                                @if ($order->isPending()) <th class="th text-right">In stock now</th> @endif
                                <th class="th text-right">Discount</th>
                                <th class="th text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->items as $item)
                                @php $short = $order->isPending() && $item->product->stock_quantity < $item->quantity; @endphp
                                <tr class="{{ $short ? 'bg-rose-50/60' : '' }}">
                                    <td class="td font-semibold text-slate-900">{{ $item->product_name }}</td>
                                    <td class="td num">{{ money($item->unit_price) }}</td>
                                    <td class="td num">{{ $item->quantity }}</td>
                                    @if ($order->isPending())
                                        <td class="td num {{ $short ? 'font-bold text-rose-600' : 'text-emerald-600' }}">{{ $item->product->stock_quantity }}</td>
                                    @endif
                                    <td class="td num text-rose-600">{{ $item->discount_amount > 0 ? '-'.money($item->discount_amount) : '-' }}</td>
                                    <td class="td num font-semibold">{{ money($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 class="card-title">Accounting entry</h2>
                    @if ($order->journalEntry) <span class="text-xs font-semibold text-slate-500">{{ $order->journalEntry->reference }}</span> @endif
                </div>
                @if ($order->journalEntry)
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50"><tr><th class="th">Account</th><th class="th text-right">Debit</th><th class="th text-right">Credit</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->journalEntry->lines as $line)
                                <tr>
                                    <td class="td">
                                        <a href="{{ route('accounting.ledger', $line->account) }}" class="font-semibold text-slate-900 hover:text-blue-600">{{ $line->account->name }}</a>
                                        <span class="ml-1 text-xs text-slate-400">{{ $line->account->code }}</span>
                                    </td>
                                    <td class="td num">{{ $line->debit > 0 ? money($line->debit) : '' }}</td>
                                    <td class="td num">{{ $line->credit > 0 ? money($line->credit) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-6 py-8 text-sm text-slate-500">
                        {{ $order->isPending() ? 'Nothing is posted yet. Completing this order will debit Accounts Receivable and credit Sales Revenue and Tax Payable.' : 'This order was cancelled, so nothing was posted.' }}
                    </p>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card p-6">
                <h2 class="card-title">Customer</h2>
                <p class="mt-3 font-semibold text-slate-900">{{ $order->customer->name }}</p>
                <p class="text-sm text-slate-500">{{ $order->customer->email }}</p>
                <p class="text-sm text-slate-500">{{ $order->customer->phone }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $order->customer->address }}</p>
            </section>

            <section class="card overflow-hidden">
                <div class="bg-linear-to-r from-blue-600 to-cyan-500 px-6 py-4 text-white"><h2 class="text-base font-bold">Totals</h2></div>
                <dl class="space-y-3 p-6 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="font-semibold tabular-nums">{{ money($order->subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Discounts</dt><dd class="font-semibold tabular-nums text-rose-600">-{{ money($order->discount_total) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-200 pt-3"><dt class="text-slate-500">Taxable amount</dt><dd class="font-semibold tabular-nums">{{ money($order->taxable_amount) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Tax ({{ rtrim(rtrim(number_format($order->tax_rate, 2), '0'), '.') }}%)</dt><dd class="font-semibold tabular-nums text-amber-600">{{ money($order->tax_amount) }}</dd></div>
                    <div class="flex items-end justify-between border-t border-slate-200 pt-4"><dt class="font-bold">Grand total</dt><dd class="text-2xl font-extrabold tabular-nums text-blue-700">{{ money($order->grand_total) }}</dd></div>
                </dl>
            </section>

            @if ($order->notes)
                <section class="card p-6"><h2 class="card-title">Notes</h2><p class="mt-2 text-sm text-slate-600">{{ $order->notes }}</p></section>
            @endif
        </aside>
    </div>
@endsection

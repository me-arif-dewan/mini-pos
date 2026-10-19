@extends('layouts.app')
@section('title', 'Invoice '.($order->invoice_number ?? $order->order_number))

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden">
            <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">&larr; Back to order</a>
            <button type="button" data-print class="btn btn-primary">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 18H4a1 1 0 01-1-1v-6a2 2 0 012-2h14a2 2 0 012 2v6a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>
                Print invoice
            </button>
        </div>

        @if (! $order->isCompleted())
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 print:hidden">
                This is a preview. The order is {{ $order->status->value }}, so no invoice number exists and nothing has been posted to the ledger.
            </div>
        @endif

        <article class="relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 print:rounded-none print:shadow-none print:ring-0">
            <div class="h-2 bg-linear-to-r from-blue-600 via-sky-500 to-cyan-400"></div>

            @unless ($order->isCompleted())
                <span class="pointer-events-none absolute right-8 top-24 rotate-12 rounded-lg border-4 border-amber-400/70 px-4 py-1 text-2xl font-extrabold text-amber-500/70">{{ $order->status->value === 'cancelled' ? 'CANCELLED' : 'DRAFT' }}</span>
            @endunless

            <div class="p-8 sm:p-10">
                <header class="flex flex-wrap items-start justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 place-items-center rounded-xl bg-linear-to-br from-blue-600 to-cyan-500 text-white">
                                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 00.7 1.7H17"/></svg>
                            </span>
                            <p class="text-xl font-extrabold text-ink">{{ config('pos.company.name') }}</p>
                        </div>
                        <address class="mt-3 text-sm not-italic leading-relaxed text-slate-500">
                            {{ config('pos.company.address') }}<br>
                            {{ config('pos.company.email') }} &middot; {{ config('pos.company.phone') }}
                        </address>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-extrabold text-blue-700">Invoice</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $order->invoice_number ?? '(not issued)' }}</p>
                        <p class="text-sm text-slate-500">Order {{ $order->order_number }}</p>
                    </div>
                </header>

                <section class="mt-8 grid gap-6 rounded-xl bg-sky-50 p-5 ring-1 ring-sky-100 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <h2 class="text-xs font-semibold text-sky-700">Billed to</h2>
                        <p class="mt-1 font-bold text-slate-900">{{ $order->customer->name }}</p>
                        <p class="text-sm text-slate-600">{{ $order->customer->address }}</p>
                        <p class="text-sm text-slate-600">{{ $order->customer->email }}@if ($order->customer->phone) &middot; {{ $order->customer->phone }}@endif</p>
                    </div>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Order date</dt><dd class="font-semibold">{{ $order->order_date->format('d M Y') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Issued</dt><dd class="font-semibold">{{ $order->completed_at?->format('d M Y') ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd><span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></dd></div>
                    </dl>
                </section>

                <table class="mt-8 min-w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-blue-600 text-xs font-semibold text-slate-500">
                            <th class="py-2 text-left">Item</th>
                            <th class="py-2 text-right">Price</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-right">Discount</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">{{ $item->product_name }}</td>
                                <td class="py-3 text-right tabular-nums">{{ money($item->unit_price) }}</td>
                                <td class="py-3 text-right tabular-nums">{{ $item->quantity }}</td>
                                <td class="py-3 text-right tabular-nums text-rose-600">
                                    @if ($item->discount_amount > 0)
                                        -{{ money($item->discount_amount) }}
                                        <span class="block text-xs text-slate-400">{{ $item->discount_type->value === 'percent' ? rtrim(rtrim(number_format($item->discount_value, 2), '0'), '.').'%' : 'fixed' }}</span>
                                    @else - @endif
                                </td>
                                <td class="py-3 text-right font-semibold tabular-nums">{{ money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6 flex flex-wrap items-start justify-between gap-8">
                    <div class="max-w-xs text-sm text-slate-500">
                        @if ($order->notes)
                            <h2 class="text-xs font-semibold text-slate-700">Notes</h2>
                            <p class="mt-1">{{ $order->notes }}</p>
                        @endif
                    </div>
                    <dl class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="tabular-nums">{{ money($order->subtotal) }}</dd></div>
                        @if ($order->items->sum('discount_amount') > 0)
                            <div class="flex justify-between"><dt class="text-slate-500">Item discounts</dt><dd class="tabular-nums text-rose-600">-{{ money($order->items->sum('discount_amount')) }}</dd></div>
                        @endif
                        @if ((float) $order->order_discount_amount > 0)
                            <div class="flex justify-between"><dt class="text-slate-500">Order discount @if ($order->discount_type?->value === 'percent') ({{ rtrim(rtrim(number_format($order->discount_value, 2), '0'), '.') }}%) @endif</dt><dd class="tabular-nums text-rose-600">-{{ money($order->order_discount_amount) }}</dd></div>
                        @endif
                        <div class="flex justify-between border-t border-slate-200 pt-2"><dt class="text-slate-500">Taxable amount</dt><dd class="tabular-nums">{{ money($order->taxable_amount) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Tax ({{ rtrim(rtrim(number_format($order->tax_rate, 2), '0'), '.') }}%)</dt><dd class="tabular-nums">{{ money($order->tax_amount) }}</dd></div>
                        <div class="flex items-center justify-between rounded-lg bg-linear-to-r from-blue-600 to-cyan-500 px-4 py-3 text-white">
                            <dt class="font-bold">Grand total</dt><dd class="text-xl font-extrabold tabular-nums">{{ money($order->grand_total) }}</dd>
                        </div>
                    </dl>
                </div>

                <section class="mt-10 break-inside-avoid rounded-xl border border-slate-200 p-5 print:hidden">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Accounting breakdown</h2>
                        <span class="text-xs font-semibold {{ $isProjected ? 'text-amber-600' : 'text-emerald-600' }}">
                            {{ $isProjected ? 'Projected, posts on completion' : 'Posted '.$order->journalEntry->reference }}
                        </span>
                    </div>
                    <table class="mt-3 min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-semibold text-slate-500">
                                <th class="py-2 text-left">Account</th>
                                <th class="py-2 text-right">Debit</th>
                                <th class="py-2 text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($journalLines as $line)
                                <tr>
                                    <td class="py-2"><span class="font-semibold text-slate-800">{{ $line['name'] }}</span> <span class="text-xs text-slate-400">{{ $line['code'] }}</span></td>
                                    <td class="py-2 text-right tabular-nums">{{ $line['debit'] > 0 ? money($line['debit']) : '' }}</td>
                                    <td class="py-2 text-right tabular-nums">{{ $line['credit'] > 0 ? money($line['credit']) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-300 font-bold">
                                <td class="py-2">Total</td>
                                <td class="py-2 text-right tabular-nums">{{ money($journalLines->sum('debit')) }}</td>
                                <td class="py-2 text-right tabular-nums">{{ money($journalLines->sum('credit')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </section>

                <footer class="mt-10 border-t border-slate-200 pt-4 text-center text-xs text-slate-400">
                    Thank you for your business. Payment is due on receipt unless agreed otherwise.
                </footer>
            </div>
        </article>
    </div>
@endsection

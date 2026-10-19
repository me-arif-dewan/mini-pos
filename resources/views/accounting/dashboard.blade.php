@extends('layouts.app')
@section('title', 'Accounting')

@section('content')
    @php
        $maxSales = max($salesByDay->max('total'), 1);
        $typeColors = [
            'asset' => 'bg-blue-100 text-blue-700 ring-blue-200',
            'liability' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'equity' => 'bg-violet-100 text-violet-700 ring-violet-200',
            'revenue' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'expense' => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Accounting dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Balances update when an order is completed.</p>
        </div>
        <span class="badge {{ $isBalanced ? 'bg-emerald-100 text-emerald-700 ring-emerald-200' : 'bg-rose-100 text-rose-700 ring-rose-200' }}">
            {{ $isBalanced ? 'Books balanced' : 'Books out of balance' }} &middot; Dr {{ money($totalDebits) }} / Cr {{ money($totalCredits) }}
        </span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Accounts receivable', $receivable, 'from-blue-600 to-blue-500', 'bg-blue-50 text-blue-700', 'Owed by customers'],
            ['Sales revenue', $revenue, 'from-emerald-600 to-emerald-500', 'bg-emerald-50 text-emerald-700', 'Net of discounts'],
            ['Tax payable', $taxPayable, 'from-amber-500 to-orange-400', 'bg-amber-50 text-amber-700', 'Collected, not yet remitted'],
            ['Pending orders', $pendingValue, 'from-cyan-600 to-sky-500', 'bg-cyan-50 text-cyan-700', ($counts['pending'] ?? 0).' awaiting completion'],
        ] as [$label, $value, $gradient, $chip, $hint])
            <div class="card relative overflow-hidden p-5">
                <div class="absolute inset-x-0 top-0 h-1 bg-linear-to-r {{ $gradient }}"></div>
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-extrabold tabular-nums text-slate-900">{{ money($value) }}</p>
                <span class="mt-3 inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $chip }}">{{ $hint }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="card-title">Net sales, last 7 days</h2>
                <p class="text-xs text-slate-500">Completed orders by completion date</p>
            </div>
            <div class="mt-6 flex h-48 items-end gap-3">
                @foreach ($salesByDay as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold tabular-nums text-slate-500">{{ $day['total'] > 0 ? money($day['total']) : '' }}</span>
                        <div class="w-full rounded-t-lg bg-linear-to-t from-blue-600 to-cyan-400" style="height: {{ max(($day['total'] / $maxSales) * 100, $day['total'] > 0 ? 4 : 1) }}%"></div>
                        <span class="text-xs font-medium text-slate-500">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="card p-6">
            <h2 class="card-title">Orders by status</h2>
            <ul class="mt-4 space-y-3">
                @foreach (['pending' => 'bg-amber-400', 'completed' => 'bg-emerald-500', 'cancelled' => 'bg-rose-400'] as $key => $dot)
                    <li class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="flex items-center gap-3 text-sm font-semibold capitalize text-slate-700"><span class="size-2.5 rounded-full {{ $dot }}"></span>{{ $key }}</span>
                        <a href="{{ route('orders.index', ['status' => $key]) }}" class="text-lg font-extrabold text-slate-900 hover:text-blue-600">{{ $counts[$key] ?? 0 }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4"><h2 class="card-title">Chart of accounts</h2></div>
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50"><tr><th class="th">Account</th><th class="th">Type</th><th class="th text-right">Balance</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($accounts as $account)
                        <tr class="hover:bg-sky-50/50">
                            <td class="td">
                                <a href="{{ route('accounting.ledger', $account) }}" class="font-semibold text-slate-900 hover:text-blue-600">{{ $account->name }}</a>
                                <span class="ml-1 text-xs text-slate-400">{{ $account->code }}</span>
                            </td>
                            <td class="td"><span class="badge {{ $typeColors[$account->type->value] }}">{{ $account->type->label() }}</span></td>
                            <td class="td num font-semibold">{{ money($account->balance) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4"><h2 class="card-title">Recent journal entries</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentEntries as $entry)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $entry->reference }}
                                <span class="font-normal text-slate-400">&middot; {{ $entry->entry_date->format('d M Y') }}</span></p>
                            @if ($entry->order) <a href="{{ route('orders.invoice', $entry->order) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Invoice</a> @endif
                        </div>
                        <p class="text-xs text-slate-500">{{ $entry->description }}</p>
                        <table class="mt-2 w-full text-xs">
                            @foreach ($entry->lines as $line)
                                <tr>
                                    <td class="py-0.5 {{ $line->credit > 0 ? 'pl-5 text-slate-500' : 'font-semibold text-slate-700' }}">{{ $line->account->name }}</td>
                                    <td class="w-24 py-0.5 text-right tabular-nums">{{ $line->debit > 0 ? money($line->debit) : '' }}</td>
                                    <td class="w-24 py-0.5 text-right tabular-nums">{{ $line->credit > 0 ? money($line->credit) : '' }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-slate-500">No entries yet. Complete an order to post the first one.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

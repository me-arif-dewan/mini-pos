@extends('layouts.app')
@section('title', $account->name.' ledger')

@section('content')
    <a href="{{ route('accounting.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">&larr; Accounting dashboard</a>

    <div class="mb-6 mt-2 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $account->name }} <span class="text-base font-medium text-slate-400">{{ $account->code }}</span></h1>
            <p class="mt-1 text-sm text-slate-500">{{ $account->type->label() }} account &middot; {{ $account->type->isDebitNormal() ? 'increases with debits' : 'increases with credits' }}</p>
        </div>
        <div class="rounded-2xl bg-linear-to-r from-blue-600 to-cyan-500 px-6 py-3 text-white shadow-sm">
            <p class="text-xs font-semibold text-blue-100">Current balance</p>
            <p class="text-2xl font-extrabold tabular-nums">{{ money($account->balance) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">Date</th>
                        <th class="th">Reference</th>
                        <th class="th">Description</th>
                        <th class="th text-right">Debit</th>
                        <th class="th text-right">Credit</th>
                        <th class="th text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($entries as $entry)
                        <tr class="hover:bg-sky-50/50">
                            <td class="td text-slate-500">{{ $entry->journalEntry->entry_date->format('d M Y') }}</td>
                            <td class="td font-semibold">
                                @if ($entry->journalEntry->order)
                                    <a href="{{ route('orders.show', $entry->journalEntry->order) }}" class="text-slate-900 hover:text-blue-600">{{ $entry->journalEntry->reference }}</a>
                                @else {{ $entry->journalEntry->reference }} @endif
                            </td>
                            <td class="td text-slate-600">{{ $entry->journalEntry->description }}</td>
                            <td class="td num">{{ $entry->debit > 0 ? money($entry->debit) : '' }}</td>
                            <td class="td num">{{ $entry->credit > 0 ? money($entry->credit) : '' }}</td>
                            <td class="td num font-semibold">{{ money($entry->balance_after) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">No postings on this account yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($entries->hasPages()) <div class="border-t border-slate-200 p-4">{{ $entries->links() }}</div> @endif
    </div>
@endsection

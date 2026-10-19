@extends('layouts.app')
@section('title', 'Orders')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Complete a pending order to deduct stock and post it to the books.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            New order
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-4">
            <div class="flex gap-1 rounded-lg bg-slate-100 p-1 text-sm font-semibold">
                @foreach ([null => 'All', 'pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                    @php $isActive = (string) $status?->value === (string) $value; @endphp
                    <a href="{{ route('orders.index', array_filter(['status' => $value, 'q' => $search])) }}"
                       class="rounded-md px-3 py-1.5 transition {{ $isActive ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">{{ $label }}</a>
                @endforeach
            </div>
            <form method="GET" class="flex gap-2">
                @if ($status) <input type="hidden" name="status" value="{{ $status->value }}"> @endif
                <input type="search" name="q" value="{{ $search }}" placeholder="Search order, invoice or customer" class="input w-64">
                <button class="btn btn-secondary">Search</button>
                @if ($search) <a href="{{ route('orders.index') }}" class="btn btn-secondary">Clear</a> @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">Order</th>
                        <th class="th">Customer</th>
                        <th class="th">Date</th>
                        <th class="th">Status</th>
                        <th class="th text-right">Total</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-sky-50/50">
                            <td class="td font-semibold text-slate-900">
                                <a href="{{ route('orders.show', $order) }}" class="hover:text-blue-600">{{ $order->order_number }}</a>
                                @if ($order->invoice_number) <span class="block text-xs font-normal text-slate-400">{{ $order->invoice_number }}</span> @endif
                            </td>
                            <td class="td">{{ $order->customer->name }}</td>
                            <td class="td text-slate-500">{{ $order->order_date->format('d M Y') }}</td>
                            <td class="td"><span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></td>
                            <td class="td num font-semibold">{{ money($order->grand_total) }}</td>
                            <td class="td text-right">
                                <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">No orders found. <a href="{{ route('orders.create') }}" class="font-semibold text-blue-600">Create the first one.</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection

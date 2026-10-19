@extends('layouts.app')
@section('title', 'Customers')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Customers</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $customers->total() }} customer{{ $customers->total() !== 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            New customer
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 p-4">
            <form method="GET" class="flex gap-2">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name, email or phone" class="input w-72">
                <button class="btn btn-secondary">Search</button>
                @if ($search) <a href="{{ route('customers.index') }}" class="btn btn-secondary">Clear</a> @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">Name</th>
                        <th class="th">Email</th>
                        <th class="th">Phone</th>
                        <th class="th">Address</th>
                        <th class="th text-right">Invoices</th>
                        <th class="th text-right">Total invoiced</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-sky-50/50">
                            <td class="td font-semibold text-slate-900">{{ $customer->name }}</td>
                            <td class="td text-slate-500">{{ $customer->email ?? '-' }}</td>
                            <td class="td text-slate-500">{{ $customer->phone ?? '-' }}</td>
                            <td class="td text-slate-500">{{ $customer->address ?? '-' }}</td>
                            <td class="td num">{{ $customer->completed_orders_count }}</td>
                            <td class="td num font-semibold">{{ money($customer->completed_orders_sum_grand_total ?? 0) }}</td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                          data-confirm="Delete {{ $customer->name }}? This cannot be undone.">
                                        @csrf @method('DELETE')
                                        <button class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">No customers found. <a href="{{ route('customers.create') }}" class="font-semibold text-blue-600">Add the first one.</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection

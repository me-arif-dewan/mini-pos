<div class="print:hidden">
    @if (session('success'))
        <div data-flash class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            <svg class="mt-0.5 size-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ session('error') }}</div>
    @endif

    @if (session('stock_errors'))
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800">
            <p class="font-bold">This order can't be completed yet. Not enough stock for:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach (session('stock_errors') as $problem)
                    <li>{{ $problem }}</li>
                @endforeach
            </ul>
            <p class="mt-2 text-rose-700">The order is still pending and nothing was deducted. Restock the products or cancel the order.</p>
        </div>
    @endif
</div>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
<div class="grid min-h-screen lg:grid-cols-2">
    <section class="hidden bg-linear-to-br from-ink via-blue-900 to-cyan-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="flex items-center gap-3">
            <span class="grid size-10 place-items-center rounded-xl bg-white/15 ring-1 ring-white/20">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 00.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </span>
            <span class="text-lg font-extrabold">Mini POS</span>
        </div>
        <div>
            <h1 class="max-w-md text-4xl font-extrabold leading-tight">Orders in, invoices out, books balanced.</h1>
            <p class="mt-4 max-w-md text-blue-100">Complete an order and stock, receivables, revenue and tax are all updated in one step.</p>
        </div>
        <div class="grid max-w-md grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10"><p class="text-xs text-blue-200">Receivable</p><p class="mt-1 font-bold">Debit</p></div>
            <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10"><p class="text-xs text-blue-200">Revenue</p><p class="mt-1 font-bold">Credit</p></div>
            <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10"><p class="text-xs text-blue-200">Tax payable</p><p class="mt-1 font-bold">Credit</p></div>
        </div>
    </section>

    <section class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">@yield('content')</div>
    </section>
</div>
</body>
</html>

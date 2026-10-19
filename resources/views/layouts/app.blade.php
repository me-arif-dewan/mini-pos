<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
@php
    $nav = [
        ['accounting.dashboard', 'Accounting',  'M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7-4h4v18h-4V3z', request()->is('accounting*')],
        ['orders.index',         'Orders',       'M4 6h16M4 12h16M4 18h10',                        request()->is('orders*')],
        ['customers.index',      'Customers',    'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a2 2 0 11-4 0 2 2 0 014 0zM5 16a2 2 0 11-4 0 2 2 0 014 0z', request()->is('customers*')],
        ['products.index',       'Products',     'M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 9h16v6H4V9zm4 2h8',  request()->is('products*')],
        ['orders.create',        'New order',    'M12 5v14M5 12h14',                               request()->is('orders/create')],
        ['settings.show',         'Settings',     'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', request()->is('settings*')],
    ];
@endphp

<div id="nav-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden print:hidden"></div>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-linear-to-b from-ink to-ink-soft text-slate-300 transition-transform lg:translate-x-0 print:hidden">
    <div class="flex items-center gap-3 px-6 py-6">
        <span class="grid size-10 place-items-center rounded-xl bg-linear-to-br from-blue-500 to-cyan-400 text-white shadow-lg shadow-cyan-500/30">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 00.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </span>
        <div>
            <p class="text-base font-extrabold leading-none text-white">Mini POS</p>
            <p class="mt-1 text-xs text-slate-400">Sales &amp; invoicing</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-2">
        @foreach ($nav as [$route, $label, $icon, $active])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-white/10 text-white ring-1 ring-white/10' : 'hover:bg-white/5 hover:text-white' }}">
                <svg class="size-5 {{ $active ? 'text-cyan-300' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <span class="grid size-9 place-items-center rounded-full bg-cyan-500/20 text-sm font-bold text-cyan-200">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="w-full rounded-lg bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white">Sign out</button>
        </form>
    </div>
</aside>

<header class="sticky top-0 z-20 flex items-center gap-3 border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur lg:hidden print:hidden">
    <button id="nav-toggle" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Toggle navigation">
        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <span class="font-extrabold text-ink">Mini POS</span>
</header>

<main class="lg:pl-64 print:pl-0">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 print:max-w-none print:p-0">
        @include('layouts.flash')
        @yield('content')
    </div>
</main>
</body>
</html>

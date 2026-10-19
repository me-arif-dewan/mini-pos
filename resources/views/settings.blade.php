@extends('layouts.app')
@section('title', 'Settings')

@php
    $tabs = [
        'pos'        => 'POS',
        'company'    => 'Company',
        'identifier' => 'Identifier',
    ];
    $s = fn(string $key, string $default = '') => old(str_replace('.', '_', $key), $settings->get($key, $default));
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">Settings</h1>
        <p class="mt-1 text-sm text-slate-500">Changes are saved to the database immediately.</p>
    </div>

    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">

        {{-- Tab sidebar --}}
        <nav class="card flex shrink-0 flex-row gap-2 p-6 lg:w-256 lg:flex-col">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('settings.show', ['tab' => $key]) }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition
                          {{ $tab === $key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    @if ($key === 'pos')
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    @elseif ($key === 'company')
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    @else
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    @endif
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Tab panels --}}
        <div class="card min-w-0 flex-1 p-6">
            <form method="POST" action="{{ route('settings.update', ['tab' => $tab]) }}">
                @csrf

                @if ($tab === 'pos')
                    <h2 class="card-title mb-5">POS Settings</h2>
                    <div class="space-y-4 max-w-sm">
                        <div>
                            <label for="tax_rate" class="label">Tax rate (%)</label>
                            <input id="tax_rate" type="number" name="tax_rate" min="0" max="100" step="0.01"
                                   value="{{ $s('pos.tax_rate', '5') }}"
                                   class="input @error('tax_rate') input-error @enderror">
                            @error('tax_rate') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="currency" class="label">Currency symbol</label>
                            <input id="currency" type="text" name="currency" maxlength="5"
                                   value="{{ $s('pos.currency_symbol', '$') }}"
                                   class="input @error('currency') input-error @enderror">
                            @error('currency') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                @elseif ($tab === 'company')
                    <h2 class="card-title mb-5">Company Details</h2>
                    <div class="space-y-4 max-w-lg">
                        <div>
                            <label for="company_name" class="label">Company name <span class="text-rose-500">*</span></label>
                            <input id="company_name" type="text" name="company_name"
                                   value="{{ $s('company.name') }}"
                                   class="input @error('company_name') input-error @enderror">
                            @error('company_name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_address" class="label">Address</label>
                            <textarea id="company_address" name="company_address" rows="2"
                                      class="input @error('company_address') input-error @enderror">{{ $s('company.address') }}</textarea>
                            @error('company_address') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_email" class="label">Email</label>
                            <input id="company_email" type="email" name="company_email"
                                   value="{{ $s('company.email') }}"
                                   class="input @error('company_email') input-error @enderror">
                            @error('company_email') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_phone" class="label">Phone</label>
                            <input id="company_phone" type="text" name="company_phone"
                                   value="{{ $s('company.phone') }}"
                                   class="input @error('company_phone') input-error @enderror">
                            @error('company_phone') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                @elseif ($tab === 'identifier')
                    <h2 class="card-title mb-1">Route Identifier</h2>
                    <p class="mb-5 text-sm text-slate-500">Controls which column is used to resolve models from URLs (e.g. <code class="rounded bg-slate-100 px-1 text-xs">/orders/{identifier}</code>).</p>
                    <div class="space-y-3 max-w-sm">
                        @foreach (['uuid' => ['UUID', 'Unguessable - best for public-facing URLs'], 'slug' => ['Slug', 'Human-readable - e.g. my-order-name'], 'id' => ['ID', 'Simple numeric - e.g. /orders/42']] as $value => [$label, $hint])
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition
                                          {{ $s('pos.identifier', 'uuid') === $value ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300' }}">
                                <input type="radio" name="identifier" value="{{ $value }}"
                                       @checked($s('pos.identifier', 'uuid') === $value)
                                       class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $label }}</p>
                                    <p class="text-xs text-slate-500">{{ $hint }}</p>
                                </div>
                            </label>
                        @endforeach
                        @error('identifier') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.guest')
@section('title', 'Sign in')

@section('content')
    <h2 class="text-2xl font-extrabold text-slate-900">Welcome back</h2>
    <p class="mt-1 text-sm text-slate-500">Sign in to manage orders and invoices.</p>

    <div class="mt-5 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-xs text-cyan-900">
        Demo account: <span class="font-bold">admin@example.com</span> / <span class="font-bold">password</span>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="input">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-cyan-500"> Keep me signed in
        </label>
        <button class="btn btn-primary w-full">Sign in</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">New here? <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Create an account</a></p>
@endsection

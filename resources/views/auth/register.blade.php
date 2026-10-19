@extends('layouts.guest')
@section('title', 'Create account')

@section('content')
    <h2 class="text-2xl font-extrabold text-slate-900">Create your account</h2>
    <p class="mt-1 text-sm text-slate-500">It takes less than a minute.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus class="input @error('name') input-error @enderror">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="input @error('password') input-error @enderror">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="input">
        </div>
        <button class="btn btn-primary w-full">Create account</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a></p>
@endsection

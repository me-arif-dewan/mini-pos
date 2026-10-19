@extends('layouts.app')
@section('title', $customer ? 'Edit '.$customer->name : 'New customer')

@section('content')
    <div class="mb-6">
        <a href="{{ route('customers.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">&larr; All customers</a>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $customer ? 'Edit '.$customer->name : 'New customer' }}</h1>
    </div>

    <div class="card mx-auto max-w-xl p-6">
        <form method="POST" action="{{ $customer ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if ($customer) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="label">Name <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name', $customer?->name) }}"
                           class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $customer?->email) }}"
                           class="input @error('email') input-error @enderror">
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="label">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $customer?->phone) }}"
                           class="input @error('phone') input-error @enderror">
                    @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="label">Address</label>
                    <textarea id="address" name="address" rows="3" class="input @error('address') input-error @enderror">{{ old('address', $customer?->address) }}</textarea>
                    @error('address') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="btn btn-primary">{{ $customer ? 'Save changes' : 'Create customer' }}</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')
@section('title', $code . ' · ' . $title)

@section('content')
    <div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center text-center">
        <p class="text-8xl font-extrabold text-slate-200">{{ $code }}</p>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900">{{ $title }}</h1>
        <p class="mt-2 max-w-md text-sm text-slate-500">{{ $message }}</p>
        <a href="{{ $back }}" class="btn btn-primary mt-8">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Go back
        </a>
    </div>
@endsection

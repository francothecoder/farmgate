@extends('layouts.app')

@section('title', 'Site settings | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Administration / content</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Site settings</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-[#14532D]">Back to overview</a>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @foreach([
                'site_name' => ['Site name', 'text'],
                'tagline' => ['Tagline', 'text'],
                'contact_email' => ['Contact email', 'email'],
                'contact_phone' => ['Contact phone', 'text'],
            ] as $key => [$label, $inputType])
                <div>
                    <label for="{{ $key }}" class="text-sm font-semibold text-slate-700">{{ $label }}</label>
                    <input id="{{ $key }}" type="{{ $inputType }}" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error($key)<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button class="rounded-xl bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Save settings</button>
            </div>
        </form>
    </section>
@endsection

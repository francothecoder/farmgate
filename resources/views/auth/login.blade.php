<x-guest-layout>
    <div class="mb-8">
        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-emerald-800">
            <span class="h-1.5 w-1.5 rounded-full bg-[#f28c28]"></span>
            Welcome back
        </span>
        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950">Log in to FarmGate</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">Pick up where you left off and stay connected to your farm community.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="grid gap-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="text-sm font-semibold text-slate-800" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="email" name="email" :value="old('email')" placeholder="you@example.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-800" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-emerald-800 hover:text-emerald-950 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex cursor-pointer items-center gap-3 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-800 focus:ring-emerald-700" name="remember">
            <span>{{ __('Keep me signed in on this device') }}</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#14532d] px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-950/10 transition hover:bg-[#104424] focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
            {{ __('Log in') }}
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </form>

    <p class="mt-7 border-t border-slate-100 pt-6 text-center text-sm text-slate-600">
        New to FarmGate?
        <a href="{{ route('register') }}" class="font-bold text-emerald-800 hover:text-emerald-950">Create your free account</a>
    </p>
</x-guest-layout>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>FarmGate Zambia | Account</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f6f7f2] font-sans text-slate-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            <aside class="relative hidden min-h-screen overflow-hidden bg-[#123b2b] px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-20">
                <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-36 -left-20 h-96 w-96 rounded-full border border-white/10"></div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f28c28] text-[#123b2b] shadow-lg shadow-black/10">
                        <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7" aria-hidden="true">
                            <path d="M20 4c-8.4 0-14 3.8-14 10.1 0 3.1 2.2 5.1 5.1 5.1C17.3 19.2 20 12.7 20 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M4 21c2.6-5.3 6.5-8.8 11.8-11.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-lg font-bold tracking-tight">FarmGate Zambia</span>
                        <span class="block text-xs text-emerald-100/75">A marketplace that grows with you</span>
                    </span>
                </a>

                <div class="relative z-10 max-w-xl py-16">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-emerald-100">
                        <span class="h-2 w-2 rounded-full bg-[#f28c28]"></span>
                        Connect. Trade. Grow.
                    </span>
                    <h1 class="mt-7 text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Good things grow when farmers connect.</h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-emerald-50/80">Find buyers, discover local produce and farm essentials, and learn from a community built around Zambian agriculture.</p>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-[#f9a24d]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true"><path d="M3 7.5h18M5.5 4h13A2.5 2.5 0 0 1 21 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5v-11A2.5 2.5 0 0 1 5.5 4Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M7 15h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <p class="mt-4 font-semibold">Trade with confidence</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-50/70">Connect directly and keep orders moving with clear updates.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-[#f9a24d]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true"><path d="M12 21s8-4 8-11V5l-8-3-8 3v5c0 7 8 11 8 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-4 font-semibold">Built for local agriculture</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-50/70">Useful products, practical knowledge, and local connections.</p>
                        </div>
                    </div>
                </div>

                <p class="relative z-10 text-xs text-emerald-100/60">Supporting the people and businesses behind every harvest.</p>
            </aside>

            <main class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-10">
                <div class="w-full max-w-2xl">
                    <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3 lg:hidden">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#14532d] text-[#f28c28]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" aria-hidden="true"><path d="M20 4c-8.4 0-14 3.8-14 10.1 0 3.1 2.2 5.1 5.1 5.1C17.3 19.2 20 12.7 20 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4 21c2.6-5.3 6.5-8.8 11.8-11.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <span class="text-lg font-bold tracking-tight text-[#123b2b]">FarmGate Zambia</span>
                    </a>

                    <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-xl shadow-slate-900/[0.04] sm:p-9">
                        {{ $slot }}
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs text-slate-500">
                        <span>© {{ date('Y') }} FarmGate Zambia</span>
                        <a href="{{ route('home') }}" class="font-medium text-emerald-800 hover:text-emerald-950">Back to marketplace</a>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>

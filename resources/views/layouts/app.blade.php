<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'FarmGate Zambia')</title>
        <meta name="description" content="@yield('meta_description', $siteSettings['tagline'] ?? 'FarmGate Zambia connects farmers, buyers, and agricultural businesses.')">
        @hasSection('meta_keywords')
            <meta name="keywords" content="@yield('meta_keywords')">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F8FAF9] text-slate-900 antialiased">
        <div class="min-h-screen">
            <header class="sticky top-0 z-50 border-b-4 border-b-orange-400 border-slate-200 bg-white/95 shadow-sm backdrop-blur-md">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#14532D] to-[#16A34A] text-lg font-bold text-white shadow-sm ring-2 ring-orange-300">F</div>
                        <div>
                            <p class="text-lg font-bold tracking-tight text-[#14532D]">{{ $siteSettings['site_name'] ?? 'FarmGate' }}</p>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">Zambia</p>
                        </div>
                    </a>

                    <nav class="hidden items-center gap-5 text-sm font-medium text-slate-700 xl:flex">
                        <a href="{{ route('home') }}" class="hover:text-[#14532D]">Home</a>
                        <a href="{{ route('listings.index') }}" class="hover:text-[#14532D]">Marketplace</a>
                        <a href="{{ route('buyer-requests.index') }}" class="hover:text-[#14532D]">Buyer Requests</a>
                        <a href="{{ route('knowledge.index') }}" class="hover:text-[#14532D]">Knowledge Hub</a>
                        <a href="{{ route('businesses.index') }}" class="hover:text-[#14532D]">Business Directory</a>
                    </nav>

                    <div class="flex items-center gap-3">
                        <form method="GET" action="{{ route('listings.index') }}" class="hidden max-w-xs items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-2 2xl:flex">
                            <input type="search" name="search" placeholder="Search products, guides..." class="w-44 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" value="{{ request('search') }}">
                            <button type="submit" class="ml-2 text-[#14532D]">Search</button>
                        </form>

                        @auth
                            <a href="{{ route('inbox.index') }}" class="hidden text-sm font-semibold text-[#14532D] sm:inline">Inbox</a>
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-[#14532D] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0f3c26]">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-slate-600 transition hover:text-orange-600 sm:text-sm">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-[#14532D]">Login</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">Join FarmGate</a>
                        @endauth
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <main>
                @yield('content')
            </main>

            <footer class="mt-16 border-t border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8">
                    <div>
                        <p class="text-lg font-bold text-[#14532D]">{{ $siteSettings['site_name'] ?? 'FarmGate Zambia' }}</p>
                        <p class="mt-3 text-sm text-slate-600">{{ $siteSettings['tagline'] ?? 'Everything agriculture, one place.' }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Explore</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li><a href="{{ route('listings.index') }}">Marketplace</a></li>
                            <li><a href="{{ route('buyer-requests.index') }}">Buyer Requests</a></li>
                            <li><a href="{{ route('knowledge.index') }}">Knowledge Hub</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Company</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li><a href="{{ route('businesses.index') }}">Directory</a></li>
                            <li><a href="{{ route('home') }}">About</a></li>
                            <li><a href="{{ route('home') }}">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Support</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li>{{ $siteSettings['contact_email'] ?? 'hello@farmgate.zm' }}</li>
                            <li>{{ $siteSettings['contact_phone'] ?? '+260 974 000000' }}</li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

<x-guest-layout>
    <div class="mb-7">
        <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-orange-800">
            <span class="h-1.5 w-1.5 rounded-full bg-[#f28c28]"></span>
            Join the community
        </span>
        <div class="mt-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-950">Create your account</h2>
                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-600">Set up your profile to discover products, connect with buyers and sellers, and share what you know.</p>
            </div>
            <p class="text-xs font-medium text-slate-500"><span class="text-orange-700">*</span> Required</p>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="grid gap-6">
        @csrf

        <section>
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-sm font-bold text-emerald-800">1</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Your profile</h3>
                    <p class="text-xs text-slate-500">Help the community know who they are connecting with.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="name" :value="__('Display name *')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="name" :value="old('name')" placeholder="How you’ll appear on FarmGate" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="full_name" :value="__('Full name')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="full_name" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="full_name" :value="old('full_name')" placeholder="Your first and last name" autocomplete="name" />
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone / WhatsApp')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="phone" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="tel" name="phone" :value="old('phone')" placeholder="+260 955 123 456" autocomplete="tel" />
                    <p class="mt-1.5 text-xs leading-5 text-slate-500">Optional. Buyers can use this number to contact you on listings.</p>
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="primary_interest" :value="__('Agricultural interest')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="primary_interest" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="primary_interest" :value="old('primary_interest')" placeholder="e.g. crops, poultry, farm supplies" />
                    <x-input-error :messages="$errors->get('primary_interest')" class="mt-2" />
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-sm font-bold text-orange-800">2</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Your location</h3>
                    <p class="text-xs text-slate-500">Location helps people find relevant local opportunities.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="country" :value="__('Country')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="country" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="country" :value="old('country', 'Zambia')" autocomplete="country-name" />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="province" :value="__('Province')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="province" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="province" :value="old('province')" placeholder="e.g. Central" autocomplete="address-level1" />
                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="district" :value="__('District')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="district" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="text" name="district" :value="old('district')" placeholder="Your district or nearest town" autocomplete="address-level2" />
                    <x-input-error :messages="$errors->get('district')" class="mt-2" />
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-sm font-bold text-emerald-800">3</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Secure your account</h3>
                    <p class="text-xs text-slate-500">Use an email you can access and a strong password.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="email" :value="__('Email address *')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="email" name="email" :value="old('email')" placeholder="you@example.com" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password *')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="password" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="password" name="password" placeholder="Create a password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm password *')" class="text-sm font-semibold text-slate-800" />
                    <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-emerald-700 focus:ring-emerald-700" type="password" name="password_confirmation" placeholder="Enter it again" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </section>

        <div class="grid gap-4">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#14532d] px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-950/10 transition hover:bg-[#104424] focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
                {{ __('Create my account') }}
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <p class="text-center text-xs leading-5 text-slate-500">You can update your profile and contact details later from your account.</p>
        </div>
    </form>

    <p class="mt-7 border-t border-slate-100 pt-6 text-center text-sm text-slate-600">
        Already have an account?
        <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:text-emerald-950">Log in</a>
    </p>
</x-guest-layout>

@extends('layouts.app')

@section('title', 'Create a listing | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Marketplace</p>
            <h1 class="mt-3 text-3xl font-bold">Create a product listing</h1>
            <p class="mt-2 text-emerald-50">Your listing will be visible after the FarmGate team reviews it.</p>
        </div>

        <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="text-sm font-semibold text-slate-700">Listing title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category_id" class="text-sm font-semibold text-slate-700">Category</label>
                    <select id="category_id" name="category_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="">Choose category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="quantity" class="text-sm font-semibold text-slate-700">Quantity</label>
                        <input id="quantity" type="number" name="quantity" min="0.01" step="0.01" value="{{ old('quantity') }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        @error('quantity')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="unit" class="text-sm font-semibold text-slate-700">Unit</label>
                        <input id="unit" name="unit" maxlength="50" value="{{ old('unit') }}" required placeholder="bags, kg, crates" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        @error('unit')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="price" class="text-sm font-semibold text-slate-700">Price (ZMW)</label>
                    <input id="price" type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('price')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 pb-3 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="negotiable" value="1" @checked(old('negotiable')) class="rounded border-slate-300 text-[#14532D]">
                        Price is negotiable
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea id="description" name="description" rows="5" required maxlength="10000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="condition" class="text-sm font-semibold text-slate-700">Condition</label>
                    <input id="condition" name="condition" maxlength="100" value="{{ old('condition') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('condition')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="location_detail" class="text-sm font-semibold text-slate-700">Location</label>
                    <input id="location_detail" name="location_detail" maxlength="255" value="{{ old('location_detail') }}" placeholder="District or nearest town" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('location_detail')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="images" class="text-sm font-semibold text-slate-700">Product photos</label>
                    <input id="images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-slate-600">
                    <p class="mt-1 text-xs text-slate-500">Add up to 5 JPG, PNG or WebP images, each up to 5 MB and at least 100 × 100 pixels. The first image is the cover.</p>
                    @error('images')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    @error('images.*')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a>
                <button class="rounded-xl bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Submit for review</button>
            </div>
        </form>
    </section>
@endsection

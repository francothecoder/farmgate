@extends('layouts.app')

@section('title', ($article->exists ? 'Edit article' : 'Create article').' | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Administration / content</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $article->exists ? 'Edit article' : 'Create article' }}</h1>
            </div>
            <a href="{{ route('admin.articles.index') }}" class="text-sm font-semibold text-[#14532D]">Back to articles</a>
        </div>

        <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if($article->exists)
                @method('PUT')
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="text-sm font-semibold text-slate-700">Title</label>
                    <input id="title" name="title" value="{{ old('title', $article->title) }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-700">URL slug</label>
                    <input id="slug" name="slug" value="{{ old('slug', $article->slug) }}" maxlength="255" placeholder="Generated from title when blank" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('slug')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category_id" class="text-sm font-semibold text-slate-700">Category</label>
                    <select id="category_id" name="category_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="">Choose category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $article->category_id) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="summary" class="text-sm font-semibold text-slate-700">Summary</label>
                    <textarea id="summary" name="summary" rows="2" maxlength="1000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">{{ old('summary', $article->summary) }}</textarea>
                    @error('summary')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="body" class="text-sm font-semibold text-slate-700">Article body</label>
                    <p class="mt-1 text-xs text-slate-500">Write in Markdown. Raw HTML is stripped when the article is rendered.</p>
                    <textarea id="body" name="body" rows="18" required maxlength="50000" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm">{{ old('body', $article->body) }}</textarea>
                    @error('body')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="text-sm font-semibold text-slate-700">Editorial status</label>
                    <select id="status" name="status" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        @foreach($statuses as $availableStatus)
                            <option value="{{ $availableStatus }}" @selected(old('status', $article->status) === $availableStatus)>{{ str_replace('_', ' ', ucfirst($availableStatus)) }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="reading_time" class="text-sm font-semibold text-slate-700">Reading time (minutes)</label>
                    <input id="reading_time" type="number" name="reading_time" min="1" max="180" value="{{ old('reading_time', $article->reading_time) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('reading_time')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tags" class="text-sm font-semibold text-slate-700">Tags</label>
                    <input id="tags" name="tags" value="{{ old('tags', $article->tags) }}" maxlength="255" placeholder="maize, soil health, irrigation" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('tags')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 pb-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured)) class="rounded border-slate-300 text-[#14532D]">
                        Feature this article
                    </label>
                </div>
                <div>
                    <label for="seo_title" class="text-sm font-semibold text-slate-700">SEO title</label>
                    <input id="seo_title" name="seo_title" value="{{ old('seo_title', $article->seo_title) }}" maxlength="255" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('seo_title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="seo_description" class="text-sm font-semibold text-slate-700">SEO description</label>
                    <textarea id="seo_description" name="seo_description" rows="2" maxlength="320" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">{{ old('seo_description', $article->seo_description) }}</textarea>
                    @error('seo_description')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="featured_image" class="text-sm font-semibold text-slate-700">Cover image</label>
                    <input id="featured_image" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-slate-600">
                    <p class="mt-1 text-xs text-slate-500">JPG, PNG or WebP, up to 5 MB. Minimum 200 × 100 pixels.</p>
                    @error('featured_image')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    @if($article->featured_image)
                        <div class="mt-3 flex items-center gap-4">
                            <img src="{{ $article->featured_image_url }}" alt="" class="h-20 w-32 rounded-xl object-cover">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remove_featured_image" value="1" @checked(old('remove_featured_image'))>
                                Remove current cover
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <button class="rounded-xl bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">{{ $article->exists ? 'Save article' : 'Create article' }}</button>
            </div>

            @if($article->exists && $article->moderationEvents->isNotEmpty())
                <section class="border-t border-slate-100 pt-5">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Editorial history</h2>
                    <ul class="mt-3 grid gap-2">
                        @foreach($article->moderationEvents as $event)
                            <li class="text-sm text-slate-600">
                                {{ str_replace('_', ' ', ucfirst($event->action)) }} by {{ $event->user?->name ?? 'Former admin' }} · {{ $event->created_at->format('M j, Y g:i a') }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </form>
    </section>
@endsection

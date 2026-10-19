<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\ModerationEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class KnowledgeArticleController extends Controller
{
    private const STATUSES = ['draft', 'in_review', 'published', 'archived'];

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $articles = KnowledgeArticle::with(['category', 'author'])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.articles.index', [
            'articles' => $articles,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.articles.form', [
            'article' => new KnowledgeArticle(['status' => 'draft']),
            'categories' => KnowledgeCategory::orderBy('name')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $validated = $this->validateArticle($request);
        $imagePath = $request->file('featured_image')?->storePublicly('knowledge', 'public');

        if ($request->hasFile('featured_image') && ! is_string($imagePath)) {
            throw new \RuntimeException('The article image could not be stored.');
        }

        try {
            $article = DB::transaction(function () use ($request, $validated, $imagePath): KnowledgeArticle {
                $status = $validated['status'];
                $article = KnowledgeArticle::create([
                    ...$this->articleAttributes($validated),
                    'slug' => $this->uniqueSlug($validated['slug'] ?? '', $validated['title']),
                    'featured_image' => $imagePath,
                    'author_id' => $request->user()->id,
                    'reviewer_id' => $status === 'published' ? $request->user()->id : null,
                    'published_at' => $status === 'published' ? now() : null,
                ]);

                $this->recordEditorialEvent($article, $request, $status, 'created');

                return $article;
            });
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article created.');
    }

    public function edit(Request $request, KnowledgeArticle $article): View
    {
        $this->authorizeAdmin($request);

        return view('admin.articles.form', [
            'article' => $article->load('moderationEvents.user'),
            'categories' => KnowledgeCategory::orderBy('name')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, KnowledgeArticle $article): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $validated = $this->validateArticle($request, $article);
        $newImagePath = $request->file('featured_image')?->storePublicly('knowledge', 'public');

        if ($request->hasFile('featured_image') && ! is_string($newImagePath)) {
            throw new \RuntimeException('The article image could not be stored.');
        }

        $oldImagePath = $article->featured_image;
        $removeImage = $request->boolean('remove_featured_image');

        try {
            DB::transaction(function () use ($request, $article, $validated, $newImagePath, $removeImage): void {
                $status = $validated['status'];
                $article->fill([
                    ...$this->articleAttributes($validated),
                    'slug' => $this->uniqueSlug($validated['slug'] ?? '', $validated['title'], $article->id),
                    'featured_image' => $newImagePath ?? ($removeImage ? null : $article->featured_image),
                    'reviewer_id' => $status === 'published' ? $request->user()->id : $article->reviewer_id,
                    'published_at' => $status === 'published'
                        ? ($article->published_at ?? now())
                        : ($status === 'archived' ? $article->published_at : null),
                ])->save();

                $this->recordEditorialEvent($article, $request, $status, 'updated');
            });
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($oldImagePath && ($newImagePath || $removeImage)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article updated.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    private function validateArticle(Request $request, ?KnowledgeArticle $article = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('knowledge_articles', 'slug')->ignore($article?->id)],
            'category_id' => ['required', 'integer', 'exists:knowledge_categories,id'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:50000'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'reading_time' => ['nullable', 'integer', 'min:1', 'max:180'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['sometimes', 'boolean'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=200,min_height=100,max_width=5000,max_height=5000'],
            'remove_featured_image' => ['sometimes', 'boolean'],
        ]);
    }

    private function articleAttributes(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'],
            'status' => $validated['status'],
            'reading_time' => $validated['reading_time'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
        ];
    }

    private function uniqueSlug(string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug !== '' ? $slug : $title) ?: 'article';
        $candidate = $base;
        $suffix = 2;

        while (KnowledgeArticle::where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }

    private function recordEditorialEvent(KnowledgeArticle $article, Request $request, string $status, string $fallback): void
    {
        $action = match ($status) {
            'published' => 'publish',
            'in_review' => 'submit_for_review',
            'archived' => 'archive',
            default => $fallback === 'created' ? 'create_draft' : 'save_draft',
        };

        $event = new ModerationEvent([
            'user_id' => $request->user()->id,
            'action' => $action,
        ]);
        $event->subject()->associate($article);
        $event->save();
    }
}

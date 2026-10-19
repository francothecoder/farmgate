<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = collect();

        if (Schema::hasTable('knowledge_articles')) {
            $query = KnowledgeArticle::with(['category', 'author'])->where('status', 'published');

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%'.$request->search.'%')
                        ->orWhere('summary', 'like', '%'.$request->search.'%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            $articles = $query->orderByDesc('is_featured')->orderByDesc('published_at')->paginate(10);
        }

        return view('knowledge.index', compact('articles'));
    }

    public function show(KnowledgeArticle $knowledgeArticle): View
    {
        abort_unless($knowledgeArticle->status === 'published', 404);

        $knowledgeArticle->load(['category', 'author']);
        $comments = $knowledgeArticle->comments()
            ->with(['user', 'replies' => fn ($query) => $query->with('user')->where('status', 'approved')->oldest()])
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->latest()
            ->paginate(20);

        return view('knowledge.show', compact('knowledgeArticle', 'comments'));
    }
}

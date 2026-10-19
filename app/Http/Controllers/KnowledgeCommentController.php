<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KnowledgeCommentController extends Controller
{
    public function store(Request $request, KnowledgeArticle $knowledgeArticle): RedirectResponse
    {
        abort_unless($knowledgeArticle->status === 'published', 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:knowledge_comments,id'],
        ]);

        $parentId = $validated['parent_id'] ?? null;

        if ($parentId !== null && ! $knowledgeArticle->comments()
            ->whereKey($parentId)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->exists()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Replies must be attached to an approved discussion in this article.',
            ]);
        }

        $knowledgeArticle->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'body' => $validated['body'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your contribution is awaiting moderator approval.');
    }
}

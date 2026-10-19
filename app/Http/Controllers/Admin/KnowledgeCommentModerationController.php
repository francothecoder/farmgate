<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KnowledgeCommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected'])],
        ]);
        $status = $validated['status'] ?? 'pending';
        $comments = KnowledgeComment::with(['article', 'user', 'parent.user'])
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.knowledge-comments.index', compact('comments', 'status'));
    }

    public function update(Request $request, KnowledgeComment $knowledgeComment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $knowledgeComment->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Discussion comment '.($validated['status'] === 'approved' ? 'approved' : 'rejected').'.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}

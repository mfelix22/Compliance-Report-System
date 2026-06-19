<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inspection_id' => ['required', 'exists:inspections,id'],
            'finding_id' => ['nullable', 'exists:findings,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'inspection_id' => $validated['inspection_id'],
            'finding_id' => $validated['finding_id'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        return back();
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'auditor'])) {
            return back()->with('error', 'You cannot delete this comment.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Messages\Message;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store($item_id, CommentRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $validated['item_id'] = $item_id;
        $validated['user_id'] = $user->id;

        try {
            Comment::create($validated);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()
                ->withInput()
                ->with('message', Message::get('comment.failed'));
        }

        return back();
    }
}

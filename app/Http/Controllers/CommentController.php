<?php

//Realizzato da: Andrea Amodeo

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'commentable_type' => ['required', 'string', Rule::in([Task::class])],
            'commentable_id' => 'required|integer|exists:tasks,id',
        ]);

        $task = Task::findOrFail($data['commentable_id']);

        $projectIds = auth()->user()->projects()->pluck('projects.id');
        abort_unless($projectIds->contains((int) $task->project_id), 403);
        
        Comment::create([
            'body' => $data['body'],
            'user_id' => auth()->id(),
            'commentable_type' => $data['commentable_type'],
            'commentable_id' => $data['commentable_id'],
        ]);

        return redirect()->route('projects.tasks.show', [$task->project_id, $task->id])->with('success', 'Commento aggiunto.');
    }
}
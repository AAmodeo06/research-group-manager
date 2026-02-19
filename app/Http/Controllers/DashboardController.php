<?php

//Realizzato da: Luigi La Gioia

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Publication;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
    {
        $user = Auth::user();

        $projects = $user->projects()
            ->withCount(['tasks', 'milestones'])
            ->get();

        $tasks = Task::where('assignee_id', $user->id)->get();

        $publications = Publication::where(function ($query) use ($user) {
            $query->whereHas('projects.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orWhereHas('authors', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->get();

        $taskStats = [
            'total'       => $tasks->count(),
            'completed'   => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'overdue'     => $tasks->filter(fn ($t) =>
                $t->status !== 'completed' &&
                $t->due_date &&
                Carbon::parse($t->due_date)->isPast()
            )->count(),
        ];

        return view('dashboard', compact(
            'user',
            'projects',
            'tasks',
            'publications',
            'taskStats'
        ));
    }
}

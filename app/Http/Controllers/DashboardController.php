<?php

// Realizzato da Luigi La Gioia

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

        $projects = collect();
        $tasks = collect();
        $publications = collect();

        if ($user->role === 'pi') {

            $projects = Project::whereHas('users', fn ($q) =>
                $q->where('users.id', $user->id)
                  ->where('project_user.role', 'pi')
            )->get();

            $tasks = Task::whereHas('project.users', fn ($q) =>
                $q->where('users.id', $user->id)
                  ->where('project_user.role', 'pi')
            )->get();

            $publications = Publication::whereHas('projects.users', fn ($q) =>
                $q->where('users.id', $user->id)
                  ->where('project_user.role', 'pi')
            )->get();
        }

        if ($user->role === 'manager') {

            $projects = Project::whereHas('users', fn ($q) =>
                $q->where('users.id', $user->id)
                  ->where('project_user.role', 'manager')
            )->get();

            $tasks = Task::whereHas('project.users', fn ($q) =>
                $q->where('users.id', $user->id)
                  ->where('project_user.role', 'manager')
            )->get();
        }

        if ($user->role === 'researcher' || $user->role === 'collaborator') {

            $tasks = Task::where('assignee_id', $user->id)->get();

            if ($user->role === 'researcher') {
                $publications = Publication::whereHas('authors', fn ($q) =>
                    $q->where('user_id', $user->id)
                )->get();
            }
        }

        $taskStats = [
            'total'       => $tasks->count(),
            'completed'   => $tasks->where('status', 'done')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'overdue'     => $tasks->filter(fn ($t) =>
                $t->status !== 'done' &&
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

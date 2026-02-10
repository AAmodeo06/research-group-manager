<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    /**
     * Display a listing of the users.
     * Export endpoint (API REST).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /**
         * Regola di accesso:
         * - PI → può esportare tutti gli utenti
         * - altri ruoli → solo utenti dei propri progetti
         */

        if ($user->global_role === 'pi') {
            return User::with('projects:id,title')
                ->select('id', 'name', 'email', 'role')
                ->orderBy('name')
                ->get();
        }

        // Manager / Researcher / Collaborator
        return User::whereHas('projects', function ($q) use ($user) {
                $q->whereIn(
                    'projects.id',
                    $user->projects->pluck('id')
                );
            })
            ->with('projects:id,title')
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();
    }
}

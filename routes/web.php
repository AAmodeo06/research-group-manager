<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    //Gestione profilo utente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:pi')->group(function () {
        Route::get('/group', [GroupController::class, 'show'])
            ->name('group.show');
        Route::post('/group/members', [GroupController::class, 'addMember'])
            ->name('group.members.add');
        Route::delete('/group/members/{member}', [GroupController::class, 'removeMember'])
            ->name('group.members.remove');
    });

    //Progetti
    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    // Fornisce i dati dei progetti in formato JSON
    Route::get('/projects/json', [ProjectController::class, 'index'])
        ->name('projects.json');

    Route::middleware('role:pi')->group(function () {

        Route::get('/projects/create', [ProjectController::class, 'create'])
            ->name('projects.create');

        Route::post('/projects', [ProjectController::class, 'store'])
            ->name('projects.store');
            
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
            ->name('projects.edit');

        Route::put('/projects/{project}', [ProjectController::class, 'update'])
            ->name('projects.update');

        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
            ->name('projects.destroy');

        // Gestione membri del progetto
        Route::get('/projects/{project}/members', [ProjectController::class, 'members'])
            ->name('projects.members');

        Route::post('/projects/{project}/members', [ProjectController::class, 'storeMember'])
            ->name('projects.members.store');

        Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'destroyMember'])
            ->name('projects.members.destroy');
    });

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');

    // Milestones (legate ai progetti)
    Route::middleware(['auth', 'verified'])->group(function () {

        Route::get('/projects/{project}/milestones/create', [MilestoneController::class, 'create'])
            ->name('milestones.create');

        Route::get('/projects/{project}/milestones/{milestone}/edit', [MilestoneController::class, 'edit'])
            ->name('milestones.edit');

        Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])
            ->name('milestones.store');

        Route::put('/projects/{project}/milestones/{milestone}', [MilestoneController::class, 'update'])
            ->name('milestones.update');

        Route::delete('/projects/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])
            ->name('milestones.destroy');
    });

    //Pubblicazione progetti
    // Index + show per utenti autenticati
    Route::get('/publications', [PublicationController::class, 'index'])
        ->name('publications.index');

    // Create + store per PI e Researcher
    Route::middleware('role:pi,researcher')->group(function () {

        Route::get('/publications/create', [PublicationController::class, 'create'])
            ->name('publications.create');

        Route::post('/publications', [PublicationController::class, 'store'])
            ->name('publications.store');
    });

    Route::get('/publications/{publication}', [PublicationController::class, 'show'])
        ->name('publications.show');

    Route::middleware('role:pi,researcher')->group(function () {

        Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])
            ->name('publications.edit');

        Route::put('/publications/{publication}', [PublicationController::class, 'update'])
            ->name('publications.update');

        Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])
            ->name('publications.destroy');
    });

    // Gestione Autori delle Pubblicazioni
    Route::middleware(['auth', 'verified', 'role:pi,researcher'])->group(function () {

        Route::post('/publications/{publication}/authors', [AuthorController::class, 'store'])
            ->name('authors.store');

        Route::put('/publications/{publication}/authors/{author}', [AuthorController::class, 'update'])
            ->name('authors.update');

        Route::delete('/publications/{publication}/authors/{author}', [AuthorController::class, 'destroy'])
            ->name('authors.destroy');
    });

    //Gestione Tasks
    Route::resource('tasks', TaskController::class);

    //Commenti
    Route::post('/comments', [CommentController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('comments.store');

    // Segna una notifica come letta
    Route::post('/notifications/{notification}/read', function ($notificationId) {
        auth()->user()->notifications()->findOrFail($notificationId)->markAsRead();
        return back();
    })->name('notifications.read');
});

Route::get('/test-401', fn () => abort(401));
Route::get('/test-403', fn () => abort(403));
Route::get('/test-404', fn () => abort(404));
Route::get('/test-419', fn () => abort(419));
Route::get('/test-500', fn () => abort(500));

require __DIR__.'/auth.php';

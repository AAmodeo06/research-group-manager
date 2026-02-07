<?php

use App\Models\Project;
use App\Models\User;

it('authenticated user can view projects index', function () {
    $user = verifiedUser();

    $this->actingAs($user)
        ->get('/projects')
        ->assertOk();
});

it('pi can access project create page', function () {
    $pi = verifiedUser(['role' => 'pi']);

    $this->actingAs($pi)
        ->get('/projects/create')
        ->assertOk();
});

it('non pi cannot access project create page', function () {
    $user = verifiedUser(['role' => 'researcher']);

    $this->actingAs($user)
        ->get('/projects/create')
        ->assertForbidden();
});

it('pi can store project and is attached as project pi', function () {
    $pi = verifiedUser(['role' => 'pi']);

    $payload = [
        'title' => 'Test Project',
        'status' => 'Open',
        'start_date' => now()->toDateString(),
        'members' => [
            $pi->id => ['role' => 'pi'],
        ],
    ];

    $response = $this->actingAs($pi)->post('/projects', $payload);

    $response->assertRedirect(route('projects.index'));

    $project = Project::where('title', 'Test Project')->first();
    expect($project)->not->toBeNull();

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $pi->id,
        'role' => 'pi',
    ]);
});

it('authenticated user can view project detail', function () {
    $user = verifiedUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get("/projects/{$project->id}")
        ->assertOk();
});

it('project members page is accessible to project pi', function () {
    $pi = verifiedUser(['role' => 'pi']);
    $project = Project::factory()->create();

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $this->actingAs($pi)
        ->get("/projects/{$project->id}/members")
        ->assertOk();
});

it('project members page is forbidden to non members', function () {
    $user = verifiedUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get("/projects/{$project->id}/members")
        ->assertForbidden();
});

it('storeMember: cannot assign project PI to a non PI global user', function () {
    $pi = verifiedUser(['role' => 'pi']);
    $nonPi = verifiedUser(['role' => 'researcher']);
    $project = Project::factory()->create();

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $this->actingAs($pi)
        ->post("/projects/{$project->id}/members", [
            'user_id' => $nonPi->id,
            'role' => 'pi',
        ])
        ->assertSessionHasErrors(['role']);
});

<?php

//Realizzato da: Cosimo Mandrillo

use App\Models\Project;
use App\Models\Group;

it('authenticated user can view projects index', function () {

    $group = Group::factory()->create();
    $user = verifiedUser(['group_id' => $group->id]);

    $this->actingAs($user)
        ->get('/projects')
        ->assertOk();
});

it('pi can access project create page', function () {

    $group = Group::factory()->create();
    $pi = verifiedUser([
        'global_role' => 'pi',
        'group_id' => $group->id
    ]);

    $this->actingAs($pi)
        ->get('/projects/create')
        ->assertOk();
});

it('non pi cannot access project create page', function () {

    $group = Group::factory()->create();
    $user = verifiedUser([
        'global_role' => 'researcher',
        'group_id' => $group->id
    ]);

    $this->actingAs($user)
        ->get('/projects/create')
        ->assertForbidden();
});

it('pi can store project and is attached as project pi', function () {

    $group = Group::factory()->create();
    $pi = verifiedUser([
        'global_role' => 'pi',
        'group_id' => $group->id
    ]);

    $payload = [
        'title' => 'Test Project',
        'status' => 'open',
        'start_date' => now()->toDateString(),
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

it('authenticated user can view project detail if member', function () {

    $group = Group::factory()->create();
    $user = verifiedUser(['group_id' => $group->id]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($user->id, ['role' => 'researcher']);

    $this->actingAs($user)
        ->get("/projects/{$project->id}")
        ->assertOk();
});

it('project members page is accessible to project pi', function () {

    $group = Group::factory()->create();
    $pi = verifiedUser([
        'global_role' => 'pi',
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $this->actingAs($pi)
        ->get("/projects/{$project->id}/members")
        ->assertOk();
});

it('project members page is forbidden to non members', function () {

    $group = Group::factory()->create();
    $user = verifiedUser(['group_id' => $group->id]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $this->actingAs($user)
        ->get("/projects/{$project->id}/members")
        ->assertForbidden();
});

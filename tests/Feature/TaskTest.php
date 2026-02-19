<?php

//Realizzato da: Andrea Amodeo

use App\Models\Task;
use App\Models\Project;
use App\Models\Group;

it('pi can create task for project', function () {

    $group = Group::factory()->create();

    $pi = verifiedUser([
        'global_role' => 'pi',
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    // collega PI al progetto
    $project->users()->attach($pi->id, ['role' => 'pi']);

    $payload = [
        'title' => 'Test Task',
        'status' => 'open',
        'priority' => 'medium',
    ];

    $response = $this->actingAs($pi)
        ->post("/projects/{$project->id}/tasks", $payload);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'project_id' => $project->id,
    ]);
});


it('manager can create task for project', function () {

    $group = Group::factory()->create();

    $manager = verifiedUser([
        'global_role' => 'manager',
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($manager->id, ['role' => 'manager']);

    $response = $this->actingAs($manager)
        ->post("/projects/{$project->id}/tasks", [
            'title' => 'Manager Task',
            'status' => 'open',
            'priority' => 'high',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'title' => 'Manager Task',
        'project_id' => $project->id,
    ]);
});


it('collaborator cannot create task', function () {

    $group = Group::factory()->create();

    $collaborator = verifiedUser([
        'global_role' => 'collaborator',
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($collaborator->id, ['role' => 'collaborator']);

    $response = $this->actingAs($collaborator)
        ->post("/projects/{$project->id}/tasks", [
            'title' => 'Forbidden Task',
            'status' => 'open',
            'priority' => 'low',
        ]);

    $response->assertForbidden();
});

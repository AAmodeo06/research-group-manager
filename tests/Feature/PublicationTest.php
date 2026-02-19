<?php

//Realizzato da: Luigi La Gioia

use App\Models\Publication;
use App\Models\Project;
use App\Models\Group;

it('authenticated user can view publications index', function () {

    $group = Group::factory()->create();

    $user = verifiedUser([
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($user->id, ['role' => 'researcher']);

    $publication = Publication::factory()->create();

    $publication->projects()->attach($project->id);

    $this->actingAs($user)
        ->get('/publications')
        ->assertOk();
});


it('pi can create publication', function () {

    $group = Group::factory()->create();

    $pi = verifiedUser([
        'global_role' => 'pi',
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $payload = [
        'title' => 'Test Publication',
        'type' => 'journal',
        'venue' => 'Nature',
        'status' => 'published',
        'projects' => [$project->id],
    ];

    $response = $this->actingAs($pi)->post('/publications', $payload);

    $response->assertRedirect(route('publications.index'));

    $this->assertDatabaseHas('publications', [
        'title' => 'Test Publication',
        'venue' => 'Nature',
    ]);
});


it('user without role cannot create publication', function () {

    $user = verifiedUser([
        'global_role' => 'collaborator'
    ]);

    $this->actingAs($user)
        ->get('/publications/create')
        ->assertForbidden();
});


it('authenticated user can view publication detail', function () {

    $group = Group::factory()->create();

    $user = verifiedUser([
        'group_id' => $group->id
    ]);

    $project = Project::factory()->create([
        'group_id' => $group->id
    ]);

    $project->users()->attach($user->id, ['role' => 'researcher']);

    $publication = Publication::factory()->create();

    $publication->projects()->attach($project->id);

    $this->actingAs($user)
        ->get("/publications/{$publication->id}")
        ->assertOk();
});

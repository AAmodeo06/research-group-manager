<?php

use App\Models\Publication;
use App\Models\Project;

it('authenticated user can view publications index', function () {
    $user = verifiedUser();

    $this->actingAs($user)
        ->get('/publications')
        ->assertOk();
});

it('pi can create publication', function () {
    $pi = verifiedUser(['role' => 'pi']);
    $project = Project::factory()->create();

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $payload = [
        'title' => 'Test Publication',
        'venue' => 'Nature',
        'year' => 2024,
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
    $user = verifiedUser(['role' => 'collaborator']);

    $this->actingAs($user)
        ->get('/publications/create')
        ->assertForbidden();
});

it('authenticated user can view publication detail', function () {
    $user = verifiedUser();
    $publication = Publication::factory()->create();

    $this->actingAs($user)
        ->get("/publications/{$publication->id}")
        ->assertOk();
});

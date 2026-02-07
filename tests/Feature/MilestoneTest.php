<?php

use App\Models\Project;

it('pi can create milestone for project', function () {

    $pi = verifiedUser(['role' => 'pi']);
    $project = Project::factory()->create();

    $project->users()->attach($pi->id, ['role' => 'pi']);

    $this->actingAs($pi)
        ->post("/projects/{$project->id}/milestones", [
            'title' => 'First milestone',
            'status' => 'open',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('milestones', [
        'title' => 'First milestone',
        'project_id' => $project->id,
        'status' => 'open',
    ]);
});

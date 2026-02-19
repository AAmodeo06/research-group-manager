<?php

//Realizzato da: Andrea Amodeo

namespace Tests\Browser;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskIndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function manager_can_view_own_tasks()
    {
        $manager = User::factory()->create([
            'email_verified_at' => now(),
            'global_role' => 'manager',
        ]);

        $project = Project::factory()->create();

        $project->users()->attach($manager->id, ['role' => 'manager']);

        Task::factory()->create([
            'title' => 'Write report',
            'project_id' => $project->id,
            'assignee_id' => $manager->id,
        ]);

        $this->browse(function (Browser $browser) use ($manager) {
            $browser->loginAs($manager)
                ->visit('/my-tasks')
                ->assertSee('Write report');
        });
    }
}

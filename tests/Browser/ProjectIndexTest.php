<?php

//Realizzato da: Cosimo Mandrillo

namespace Tests\Browser;

use App\Models\User;
use App\Models\Project;
use App\Models\Group;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProjectIndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_pi_can_view_projects_index_with_content()
    {
        $group = Group::factory()->create();

        $pi = User::factory()->create([
            'email_verified_at' => now(),
            'global_role' => 'pi',
            'group_id' => $group->id,
        ]);

        $project = Project::factory()->create([
            'title' => 'Quantum AI Research',
            'group_id' => $group->id,
        ]);

        $project->users()->attach($pi->id, ['role' => 'pi']);

        $this->browse(function (Browser $browser) use ($pi) {
            $browser->loginAs($pi)
                ->visit('/projects')
                ->pause(1000)
                ->assertSee('Quantum AI Research');
        });
    }
}

<?php

//Realizzato da: Luigi La Gioia

namespace Tests\Browser;

use App\Models\User;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Group;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PublicationIndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_publications_index_with_content()
    {
        // 1️⃣ Creiamo gruppo
        $group = Group::factory()->create();

        // 2️⃣ Creiamo PI nel gruppo
        $pi = User::factory()->create([
            'email_verified_at' => now(),
            'global_role' => 'pi',
            'group_id' => $group->id,
        ]);

        // 3️⃣ Creiamo progetto nel gruppo
        $project = Project::factory()->create([
            'group_id' => $group->id,
        ]);

        // 4️⃣ Colleghiamo PI al progetto
        $project->users()->attach($pi->id, ['role' => 'pi']);

        // 5️⃣ Creiamo publication
        $publication = Publication::factory()->create([
            'title' => 'AI in Medical Imaging',
            'status' => 'published',
        ]);

        // 6️⃣ Colleghiamo publication al progetto
        $publication->projects()->attach($project->id);

        $this->browse(function (Browser $browser) use ($pi) {
            $browser->loginAs($pi)
                ->visit('/publications')
                ->assertSee('Publications')
                ->assertSee('AI in Medical Imaging');
        });
    }

    /** @test */
    public function guest_cannot_access_publications()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/publications')
                ->assertPathIs('/login');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProjectIndexTest extends DuskTestCase
{
    /** @test */
    public function user_can_view_projects_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'pi',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit('/projects')
                ->assertSee('Projects');
        });
    }
}

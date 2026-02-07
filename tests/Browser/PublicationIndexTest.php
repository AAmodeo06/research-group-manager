<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PublicationIndexTest extends DuskTestCase
{
    /** @test */
    public function user_can_view_publications_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'researcher',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit('/publications')
                ->assertSee('Publications');
        });
    }
}

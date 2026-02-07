<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskIndexTest extends DuskTestCase
{
    /** @test */
    public function user_can_view_tasks_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'manager',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit('/tasks')
                ->assertSee('Tasks');
        });
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Group;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Task;
use App\Models\Tag;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 0) CREAZIONE UTENTI GLOBALI (Senza gruppo associato)
        $globalRoles = ['pi', 'manager', 'researcher', 'collaborator'];
        
        foreach ($globalRoles as $role) {
            User::factory()->create([
                'name' => ucfirst($role) . ' Global',
                'email' => $role . '_global@example.com',
                'password' => Hash::make('password'),
                'global_role' => $role,
                'group_id' => null, // Non appartengono a un gruppo specifico
                'email_verified_at' => now(),
            ]);
        }

        // 1) Gruppi
        $groups = Group::factory()->count(3)->create();

        // 2) Utenti legati ai Gruppi
        $allUsers = collect();

        foreach ($groups as $group) {

            $pi = User::factory()->create([
                'email' => 'pi_' . $group->id . '@example.com',
                'name'  => 'PI ' . $group->id,
                'password' => Hash::make('password'),
                'global_role' => 'pi',
                'role'  => 'pi',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $manager = User::factory()->create([
                'email' => 'manager_' . $group->id . '@example.com',
                'name'  => 'Manager ' . $group->id,
                'password' => Hash::make('password'),
                'global_role' => 'manager',
                'role'  => 'manager',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $researcher = User::factory()->create([
                'email' => 'researcher_' . $group->id . '@example.com',
                'name'  => 'Researcher ' . $group->id,
                'password' => Hash::make('password'),
                'global_role' => 'researcher',
                'role'  => 'researcher',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $collaborator = User::factory()->create([
                'email' => 'collaborator_' . $group->id . '@example.com',
                'name'  => 'Collaborator ' . $group->id,
                'password' => Hash::make('password'),
                'global_role' => 'collaborator',
                'role'  => 'collaborator',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $extraUsers = User::factory()->count(5)->create([
                'group_id' => $group->id,
                'global_role' => 'researcher',
                'role' => 'researcher',
                'email_verified_at' => now(),
            ]);

            $allUsers = $allUsers
                ->merge([$pi, $manager, $researcher, $collaborator])
                ->merge($extraUsers);
        }

        // 3) Tag
        $tags = Tag::factory()->count(8)->create();

        // 4) Progetti (group-centric)
        foreach ($groups as $group) {
            $groupUsers = $allUsers->where('group_id', $group->id);
            $piForProject = $groupUsers->firstWhere('global_role', 'pi');

            $projects = Project::factory()->count(2)->create([
                'group_id' => $group->id,
            ]);

            foreach ($projects as $project) {
                // Assegnazione PI
                $project->users()->attach($piForProject->id, [
                    'role' => 'pi',
                    'effort' => 0.5,
                ]);

                // Assegnazione altri 2 membri casuali del gruppo
                $members = $groupUsers->where('id', '!=', $piForProject->id)->random(2);
                foreach ($members as $m) {
                    $project->users()->attach($m->id, [
                        'role' => $m->global_role,
                        'effort' => $faker->randomFloat(2, 0.1, 0.4),
                    ]);
                }

                // Milestone
                Milestone::factory()->count(2)->create(['project_id' => $project->id]);

                // Task
                Task::factory()->count(3)->create([
                    'project_id' => $project->id,
                    'assignee_id' => $groupUsers->random()->id
                ]);
            }
        }
    }
}
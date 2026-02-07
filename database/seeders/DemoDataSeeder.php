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

        // 1) Gruppi
        $groups = Group::factory()->count(3)->create();

        // 2) Utenti
        $allUsers = collect();

        foreach ($groups as $group) {

            $pi = User::factory()->create([
                'email' => 'pi_' . $group->id . '@example.com',
                'name'  => 'PI ' . $group->id,
                'password' => Hash::make('password'),
                'role'  => 'pi',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $manager = User::factory()->create([
                'email' => 'manager_' . $group->id . '@example.com',
                'name'  => 'Manager ' . $group->id,
                'password' => Hash::make('password'),
                'role'  => 'manager',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $researcher = User::factory()->create([
                'email' => 'researcher_' . $group->id . '@example.com',
                'name'  => 'Researcher ' . $group->id,
                'password' => Hash::make('password'),
                'role'  => 'researcher',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $collaborator = User::factory()->create([
                'email' => 'collaborator_' . $group->id . '@example.com',
                'name'  => 'Collaborator ' . $group->id,
                'password' => Hash::make('password'),
                'role'  => 'collaborator',
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            $extraUsers = User::factory()->count(8)->create([
                'group_id' => $group->id,
                'role' => $faker->randomElement(['researcher', 'collaborator']),
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
            $piForProject = $groupUsers->firstWhere('role', 'pi');

            $projects = Project::factory()->count(3)->create([
                'group_id' => $group->id,
            ]);

            foreach ($projects as $project) {

                // PI nel progetto
                $project->users()->attach($piForProject->id, [
                    'role' => 'pi',
                    'effort' => 0.3,
                ]);

                // altri membri
                $members = $groupUsers
                    ->where('id', '!=', $piForProject->id)
                    ->random(3);

                foreach ($members as $m) {
                    $project->users()->syncWithoutDetaching([
                        $m->id => [
                            'role' => $m->role,
                            'effort' => $faker->randomFloat(2, 0.1, 0.8),
                        ],
                    ]);
                }

                // Milestone
                Milestone::factory()->count(3)->create([
                    'project_id' => $project->id,
                ]);

                // Task
                Task::factory()->count(6)->make()->each(function ($t) use ($project, $groupUsers) {
                    $t->project_id = $project->id;
                    $t->assignee_id = $groupUsers->random()->id;
                    $t->save();
                });

                // Pubblicazioni
                $pubs = Publication::factory()->count(2)->create();

                foreach ($pubs as $pub) {

                    $project->publications()->attach($pub->id);

                    $coauthors = $groupUsers->random(3)->values();

                    foreach ($coauthors as $i => $u) {
                        Author::create([
                            'publication_id' => $pub->id,
                            'user_id' => $u->id,
                            'position' => $i + 1,
                            'is_corresponding' => $i === 0,
                        ]);
                    }

                    $pub->tags()->attach($tags->random(2)->pluck('id'));

                    $pub->attachments()->create([
                        'title' => 'Manuscript PDF',
                        'path' => 'docs/' . $pub->id . '_manuscript.pdf',
                        'uploaded_by' => $piForProject->id,
                    ]);

                    $pub->comments()->create([
                        'user_id' => $piForProject->id,
                        'body' => 'Prima bozza pronta per revisione.',
                    ]);
                }

                // Tag progetto
                $project->tags()->attach($tags->random(3)->pluck('id'));

                // Allegato progetto
                $project->attachments()->create([
                    'title' => 'Project Plan',
                    'path' => 'docs/' . $project->code . '_plan.pdf',
                    'uploaded_by' => $piForProject->id,
                ]);

                $project->comments()->create([
                    'user_id' => $piForProject->id,
                    'body' => 'Benvenuti nel progetto ' . $project->title . '!',
                ]);
            }
        }
    }
}

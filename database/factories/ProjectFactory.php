<?php

//Realizzato da: Cosimo Mandrillo

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Group;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{

    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id'    => Group::factory(),
            'title'       => $this->faker->sentence(3),
            'code'        => strtoupper($this->faker->bothify('PRJ-#####')),
            'funder'      => $this->faker->randomElement(['MUR','EU','MIUR','Regione Puglia']),
            'start_date'  => $this->faker->date(),
            'end_date'    => null,
            'status'      => $this->faker->randomElement(['active','completed','on-hold']),
            'description' => $this->faker->paragraph(),
        ];
    }
}

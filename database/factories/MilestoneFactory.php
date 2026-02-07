<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Milestone;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Milestone>
 */
class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'    => 'Milestone: ' . $this->faker->words(2, true),
            'due_date' => $this->faker->date(),
            'status'   => $this->faker->randomElement(['open','in_progress','done']),
        ];
    }
}

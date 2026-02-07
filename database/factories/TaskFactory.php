<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => 'Task: ' . $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'due_date'    => $this->faker->optional()->date(),
            'status'      => $this->faker->randomElement(['open','in_progress','done']),
            'priority'    => $this->faker->randomElement(['low','medium','high']),
        ];
    }
}

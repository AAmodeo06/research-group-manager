<?php

//Realizzato da: Cosimo Mandrillo

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attachment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path'        => 'docs/' . $this->faker->uuid . '.pdf',
            'title'       => $this->faker->sentence(3),
            'type'        => null,
            'version'     => 1,
            'uploaded_by' => User::factory(),
        ];
    }
}

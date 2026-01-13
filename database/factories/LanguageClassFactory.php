<?php

namespace Database\Factories;

use App\Models\LanguageClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LanguageClass>
 */
class LanguageClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = LanguageClass::class;

    public function definition(): array
    {

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'professor_id' => User::factory()->professor()->create()->id,
            'schedule_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'status' => 'scheduled',
        ];
    }

    // Optional state helpers
    public function scheduled()
    {
        return $this->state(fn() => ['status' => 'assigned']);
    }

    public function completed()
    {
        return $this->state(fn() => ['status' => 'completed']);
    }

    public function professor(): static
    {
        return $this->state(fn() => ['role' => 'professor']);
    }

    public function student(): static
    {
        return $this->state(fn() => ['role' => 'student']);
    }
}

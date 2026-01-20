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
            'professor_id' => User::factory()->professor(),
            'schedule_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'status' => 'scheduled',
        ];
    }

    // Optional state helpers
    public function scheduled()
    {
        return $this->state(fn() => ['status' => 'scheduled']);
    }

    /**
     * Factory state for completed classes
     */
    public function completed()
    {
        return $this->state(fn() => ['status' => 'completed']);
    }

    /**
     * Factory state for a specific professor
     */
    public function forProfessor(User $professor): static
    {
        return $this->state(fn() => ['professor_id' => $professor->id]);
    }

    /**
     * Factory state to assign a student to the class after creation
     */
    public function forStudent(User $student): static
    {
        return $this->afterCreating(function (LanguageClass $class) use ($student) {
            $class->students()->attach($student->id, ['status' => 'assigned']);
        });
    }
}

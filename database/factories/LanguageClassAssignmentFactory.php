<?php

namespace Database\Factories;

use App\Models\LanguageClass;
use App\Models\LanguageClassAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LanguageClassAssignment>
 */
class LanguageClassAssignmentFactory extends Factory
{
    protected $model = LanguageClassAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // If you pass 'language_class_id' in create(), it will be used; otherwise create a new LanguageClass
            'language_class_id' => $this->language_class_id ?? LanguageClass::factory(),

            // If you pass 'student_id' in create(), it will be used; otherwise create a new student
            'student_id' => $this->student_id ?? User::factory()->student(),

            // Default status
            'status' => 'assigned',
        ];
    }

    /**
     * Optional states
     */
    public function assigned(): static
    {
        return $this->state(fn() => ['status' => 'assigned']);
    }

    public function passed(): static
    {
        return $this->state(fn() => ['status' => 'passed']);
    }

    public function failed(): static
    {
        return $this->state(fn() => ['status' => 'failed']);
    }
}

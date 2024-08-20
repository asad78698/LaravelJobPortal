<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name,
            'user_id'=> 1,
            'category_id'=> rand(1,5),
            'jobtypes_id' => rand(1,5),
            'vacancies' => rand(1,5),
            'location' => fake()->city(),
            'description' => fake()->text(),
            'experience' => rand(1,10),
            'companywebsite' => fake()->name()

        ];
    }
}

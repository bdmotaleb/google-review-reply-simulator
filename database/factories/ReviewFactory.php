<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);
        $sentiments = ['positive', 'neutral', 'negative'];
        
        return [
            'reviewer_name' => fake()->name(),
            'rating' => $rating,
            'review_text' => fake()->paragraph(),
            'sentiment' => fake()->randomElement($sentiments),
        ];
    }
}

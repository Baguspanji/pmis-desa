<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(5, 10));
        $categories = ['Kesehatan', 'Wisata', 'Pariwisata', 'Pendidikan', 'Ekonomi', 'Sosial', 'Budaya', 'Pembangunan'];

        return [
            'title' => rtrim($title, '.'),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(2),
            'content' => fake()->paragraphs(rand(5, 10), true),
            'image' => null, // Set to null or use a placeholder service
            'category' => fake()->randomElement($categories),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'is_published' => fake()->boolean(80), // 80% chance of being published
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'created_by' => 1, // Assuming user with ID 1 exists
        ];
    }

    /**
     * Indicate that the news is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the news is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the news is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}

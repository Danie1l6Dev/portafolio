<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AchievementType;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Achievement> */
final class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'type' => fake()->randomElement(AchievementType::cases()),
            'organization' => fake()->company(),
            'result' => fake()->randomElement(['Ganador', 'Finalista', 'Certificación obtenida']),
            'role' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'achieved_at' => fake()->dateTimeBetween('-3 years', 'now'),
            'external_url' => fake()->optional()->url(),
            'is_featured' => false,
            'is_visible' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }

    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_visible' => false]);
    }
}

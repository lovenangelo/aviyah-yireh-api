<?php

namespace Database\Factories;

use App\Models\Events;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Events>
 */
class EventsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => User::inRandomOrder()->first()->id ?? null,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'start_at' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'end_at' => $this->faker->dateTimeBetween('+2 days', '+2 months'),
            'image_url' => $this->faker->imageUrl()
        ];
    }
}

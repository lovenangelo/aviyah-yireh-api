<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Events>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
        ];
    }

    public function aviyahAdvertising(): array
    {
        return [
            'name' => 'Aviyah - Advertising',
        ];
    }

    public function aviyahConstruction(): array
    {
        return [
            'name' => 'Aviyah - Construction',
        ];
    }

    public function yirehCarServices(): array
    {
        return [
            'name' => 'Yireh - Car Services',
        ];
    }
}

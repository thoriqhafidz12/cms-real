<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mNama' => $this->faker->word(),
            'mRoute' => $this->faker->word(),
            'mParentId' => null,
            'mIcon' => $this->faker->word(),
            'mOrder' => $this->faker->numberBetween(1, 10),
            'mIsActive' => $this->faker->boolean(),
            'mCreatedAt' => now(),
            'mCreatedBy' => $this->faker->word(),
            'mUpdatedAt' => now(),
            'mUpdatedBy' => $this->faker->word(),
        ];
    }
}

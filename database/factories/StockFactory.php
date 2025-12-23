<?php

namespace Database\Factories;

use App\Value\CurrencyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'isin' => strtoupper($this->faker->bothify('??##########')),
            'display_name' => $this->faker->name,
            'type' => $this->faker->randomElement(['ETF', 'S']),
            'ticker' => strtoupper($this->faker->lexify('???')),
            'currency' => $this->faker->randomElement(CurrencyType::class),
            'price' => $this->faker->numberBetween(1, 10000),
        ];
    }
}

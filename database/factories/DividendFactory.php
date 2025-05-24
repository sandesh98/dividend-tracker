<?php

namespace Database\Factories;

use App\Value\CurrencyType;
use App\Value\DividendType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dividend>
 */
class DividendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => StockFactory::new(),
            'date' => $this->faker->dateTimeThisYear()->format('d-m-Y'),
            'time' => $this->faker->time(),
            'fx' => $this->faker->randomFloat(4, 0.5, 1.5),
            'amount' => $this->faker->numberBetween(1, 10000),
            'currency' => $this->faker->randomElement([CurrencyType::USD->value, CurrencyType::EUR->value]),
            'description' => $this->faker->randomElement([DividendType::Dividend->value, DividendType::DividendTax->value]),
        ];
    }
}

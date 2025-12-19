<?php

namespace Database\Factories;

use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Money\Currency;
use Brick\Money\Money;
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
            'paid_out_at' => $this->faker->dateTime(),
            'fx' => $this->faker->randomFloat(4, 0.5, 1.5),
            'dividend_amount' => $this->faker->passthrough(
                Money::ofMinor($this->faker->randomNumber(),
                $this->faker->randomElement(['EUR', 'USD'])),
            ),
            'description' => $this->faker->randomElement([DividendType::Dividend->value, DividendType::DividendTax->value]),
        ];
    }
}

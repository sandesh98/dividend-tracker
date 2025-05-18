<?php

namespace Database\Factories;

use App\Value\CurrencyType;
use App\Value\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trade>
 */
class TradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeThisYear()->format('d-m-Y'),
            'time' => $this->faker->time(),
            'stock_id' => StockFactory::new(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'action' => $this->faker->randomElement([TransactionType::Buy->value, TransactionType::Sell->value]),
            'currency' => $this->faker->randomElement([CurrencyType::USD->value, CurrencyType::EUR->value]),
            'price_per_unit' => $this->faker->numberBetween(1, 10000),
            'total_transaction_value' => $this->faker->numberBetween(1, 10000),
            'fx' => $this->faker->randomFloat(4, 0.5, 1.5),
            'description' => function ($attributes) {
                return sprintf(
                    '%s %s @ %s %s',
                    $attributes['action'],
                    $attributes['quantity'],
                    $attributes['price_per_unit'],
                    $attributes['currency'],
                );
            },
            'order_id' => $this->faker->uuid()
        ];
    }
}

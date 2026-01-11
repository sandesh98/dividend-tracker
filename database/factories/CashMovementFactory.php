<?php

namespace Database\Factories;

use App\Value\CashMovementType;
use App\Value\CurrencyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashMovement>
 */
class CashMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'occurred_at' => $this->faker->dateTime(),
            'total_transaction_value' => $this->faker->numberBetween(100, 100000),
            'currency' => CurrencyType::EUR->value,
            'description' => CashMovementType::cases(),
        ];
    }
}

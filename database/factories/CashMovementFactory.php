<?php

namespace Database\Factories;

use App\Value\CurrencyType;
use App\Value\DescriptionType;
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
            'date' => $this->faker->dateTimeThisYear()->format('d-m-Y'),
            'time' => $this->faker->time(),
            'total_transaction_value' => $this->faker->numberBetween(100, 100000),
            'currency' => CurrencyType::EUR->value,
            'description' => $this->faker->randomElement(
                [DescriptionType::Deposit->value, DescriptionType::Withdrawal->value]
            ),
        ];
    }
}

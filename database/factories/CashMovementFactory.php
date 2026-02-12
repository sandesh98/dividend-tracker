<?php

namespace Database\Factories;

use App\Value\CashMovementType;
use Brick\Money\Money;
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
            'amount' => $this->faker->passthrough(
                Money::ofMinor(
                    $this->faker->randomNumber(),
                    $this->faker->randomElement(['EUR', 'USD'])
                ),
            ),
            'description' => $this->faker->randomElement(CashMovementType::cases()),
            'occurred_at' => $this->faker->dateTime(),
        ];
    }
}

<?php

namespace Tests\Feature\Services\Transactions;

use App\Services\Transactions\CalculateAvailableCash;
use App\Value\CashMovementType;
use App\Value\CurrencyType;
use Brick\Money\Money;
use Database\Factories\CashMovementFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateAvailableCashTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_available_cash(): void
    {
        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(500, CurrencyType::EUR->value),
            'description' => CashMovementType::Deposit,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(50, CurrencyType::EUR->value),
            'description' => CashMovementType::Deposit,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(200, CurrencyType::EUR->value),
            'description' => CashMovementType::Withdrawal,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(20, CurrencyType::EUR->value),
            'description' => CashMovementType::Withdrawal,
        ]);

        $service = app(CalculateAvailableCash::class)->__invoke();

        // Deposits: 500 + 50 = 550
        // Withdrawals: 200 + 20 = 220
        // Total: 550 - 220 = 330
        $this->assertEquals(330, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

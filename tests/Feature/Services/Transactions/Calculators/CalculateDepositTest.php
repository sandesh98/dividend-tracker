<?php

namespace Tests\Feature\Services\Transactions\Calculators;

use App\Services\Transactions\Calculators\CalculateDeposit;
use App\Value\CashMovementType;
use App\Value\CurrencyType;
use Brick\Money\Money;
use Database\Factories\CashMovementFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateDepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_deposits(): void
    {
        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(200, CurrencyType::EUR->value),
            'description' => CashMovementType::Deposit,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'amount' => Money::of(50, CurrencyType::EUR->value),
            'description' => CashMovementType::Deposit,
        ]);

        $service = app(CalculateDeposit::class)->__invoke();

        // Total = 200 + 50 = 250
        $this->assertEquals(250, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

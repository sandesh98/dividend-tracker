<?php

namespace Tests\Feature\Services\Transactions\Calculators;

use App\Services\Transactions\Calculators\CalculateDeposit;
use App\Value\CashMovementType;
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
            'description' => CashMovementType::Deposit,
            'total_transaction_value' => 200,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'description' => CashMovementType::Deposit,
            'total_transaction_value' => 50,
        ]);

        $service = app(CalculateDeposit::class)->__invoke();

        // Total = 200 + 50 = 250
        $this->assertEquals(250, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

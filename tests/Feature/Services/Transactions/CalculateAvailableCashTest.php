<?php

namespace Tests\Feature\Services\Transactions;

use App\Services\Transactions\CalculateAvailableCash;
use App\Value\DescriptionType;
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
            'description' => DescriptionType::Deposit->value,
            'total_transaction_value' => 500,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'description' => DescriptionType::Deposit->value,
            'total_transaction_value' => 50,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'description' => DescriptionType::Withdrawal->value,
            'total_transaction_value' => 200,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'description' => DescriptionType::Withdrawal->value,
            'total_transaction_value' => 20,
        ]);

        $service = app(CalculateAvailableCash::class)->__invoke();

        // Deposits: 500 + 50 = 550
        // Withdrawals: 200 + 20 = 220
        // Total: 550 - 220 = 330
        $this->assertEquals(330, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

<?php

namespace Tests\Feature\Services\Transactions\Calculators;

use App\Services\Transactions\Calculators\CalculateWithdrawal;
use App\Value\DescriptionType;
use Brick\Money\Money;
use Database\Factories\CashMovementFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_deposits(): void
    {
        CashMovementFactory::new()->createOneQuietly([
            'description' => DescriptionType::Withdrawal->value,
            'total_transaction_value' => 200,
        ]);

        CashMovementFactory::new()->createOneQuietly([
            'description' => DescriptionType::Withdrawal->value,
            'total_transaction_value' => 50,
        ]);

        $service = app(CalculateWithdrawal::class)->__invoke();

        // Total = 200 + 50 = 250
        $this->assertEquals(250, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

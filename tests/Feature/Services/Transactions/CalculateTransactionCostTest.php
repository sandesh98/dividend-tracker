<?php

namespace Tests\Feature\Services\Transactions;

use App\Services\Transactions\CalculateTransactionCost;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Money\Money;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateTransactionCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_transaction_cost(): void
    {
        TradeFactory::new()->createOneQuietly([
            'description' => DescriptionType::DegiroTransactionCost,
            'currency' => CurrencyType::EUR,
            'total_transaction_value' => 100,
        ]);

        TradeFactory::new()->createOneQuietly([
            'description' => DescriptionType::DegiroTransactionCost,
            'currency' => CurrencyType::EUR,
            'total_transaction_value' => 150,
        ]);

        TradeFactory::new()->createOneQuietly([
            'description' => DescriptionType::DegiroTransactionCost,
            'currency' => CurrencyType::EUR,
            'total_transaction_value' => 200,
        ]);

        TradeFactory::new()->createOneQuietly([
            'description' => DescriptionType::CurrencyDebit, // Should be excluded.
            'currency' => CurrencyType::EUR,
            'total_transaction_value' => 100,
        ]);

        $service = app(CalculateTransactionCost::class)->__invoke();

        // Total: 100 + 150 + 200 = 450
        $this->assertEquals(450, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

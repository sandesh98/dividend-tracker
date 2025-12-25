<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\CalculateNetSold;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Brick\Money\Money;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateNetSoldTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_sold_in_euro(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Koop 2 @ 10 EUR',
                'quantity' => 2,
                'action' => TransactionType::Buy,
                'total_transaction_value' => 2000,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Verkoop 1 @ 10 EUR',
                'quantity' => 1,
                'action' => TransactionType::Sell,
                'total_transaction_value' => 1000,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Verkoop 2 @ 20 EUR',
                'quantity' => 1,
                'action' => TransactionType::Sell,
                'total_transaction_value' => 4000,
                'order_id' => 'order-3',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-3',
            ]);

        $service = app(CalculateNetSold::class)->__invoke($stock);

        // Calculation:
        // Sell: 1000 + 4000 = 5000
        // Total: 5000
        $this->assertEquals(5000, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

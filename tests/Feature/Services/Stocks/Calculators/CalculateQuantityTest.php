<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\CalculateQuantity;
use App\Value\TransactionType;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateQuantityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_stock_quantity(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'quantity' => 5,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'quantity' => 3,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Sell->value,
                'quantity' => 4,
            ]);

        $service = app(CalculateQuantity::class)->__invoke($stock);

        // Buy: 5 + 3 = 8
        // Sell: 4
        // Total: 8 - 4 = 4
        $this->assertEquals(4, $service);
        $this->assertIsInt($service);
    }

    public function test_it_return_zero_when_more_stock_where_sold(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'quantity' => 5,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'quantity' => 3,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Sell->value,
                'quantity' => 9,
            ]);

        $service = app(CalculateQuantity::class)->__invoke($stock);

        // Buy: 5 + 3 = 8
        // Sell: 9
        // Total: 8 - 9 = -1 should be 0
        $this->assertEquals(0, $service);
        $this->assertIsInt($service);
    }
}

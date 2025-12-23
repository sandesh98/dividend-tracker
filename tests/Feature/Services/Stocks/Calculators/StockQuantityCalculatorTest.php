<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\StockQuantityCalculator;
use App\Value\TransactionType;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockQuantityCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function testItCalculatesStockQuantity(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 5,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 3,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'quantity' => 4,
            ]);

        $service = app(StockQuantityCalculator::class)->calculate($stock);

        // Buy: 5 + 3 = 8
        // Sell: 4
        // Total: 8 - 4 = 4
        $this->assertEquals(4, $service);
        $this->assertIsInt($service);
    }

    public function testItReturnZeroWhenMoreStockWhereSold(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 5,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 3,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'quantity' => 9,
            ]);

        $service = app(StockQuantityCalculator::class)->calculate($stock);

        // Buy: 5 + 3 = 8
        // Sell: 9
        // Total: 8 - 9 = -1 should be 0
        $this->assertEquals(0, $service);
        $this->assertIsInt($service);
    }
}

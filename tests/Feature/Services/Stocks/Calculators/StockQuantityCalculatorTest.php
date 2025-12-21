<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\StockService;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
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

        $service = app(StockService::class);

        $data = $service->quantity($stock);

        $this->assertEquals((5 + 3 - 4), $data);
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

        $service = app(StockService::class);

        $data = $service->quantity($stock);

        // Calculation
        // Buy: 5 + 3 = 8
        // Sell: 9
        $this->assertEquals(0, $data);
    }
}

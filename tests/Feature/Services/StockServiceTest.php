<?php

namespace Tests\Feature\Services;

use App\Models\Stock;
use App\Services\Stocks\StockService;
use App\Value\TransactionType;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
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

        $data = $service->getStockQuantity($stock);

        $this->assertEquals(4, $data);
    }

    public function testItCalculatesAverageStockPrice(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 20,
                'total_transaction_value' => 1000,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'quantity' => 10,
                'total_transaction_value' => 5500,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'quantity' => 15,
                'total_transaction_value' => 2000,
            ]);

        $service = app(StockService::class);

        $data = $service->getAverageStockPrice($stock);

        $this->assertEquals(300, $data->toInt());
    }
}

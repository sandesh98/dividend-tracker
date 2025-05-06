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
        $stock = StockFactory::new()
            ->createOne([
            'name' => 'VANGUARD FTSE ALL-WORLD HIGH DIV',
        ]);

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

        $quantity = $service->getStockQuantity($stock->name);

        $this->assertEquals(4, $quantity);
    }

    public function testItCalculatesTotalValue(): void
    {
        $stock = StockFactory::new();
    }
}

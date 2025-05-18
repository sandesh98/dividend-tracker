<?php

namespace Tests\Feature\Services;

use App\Services\Stocks\StockService;
use App\Value\CurrencyType;
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

        $this->assertEquals((5 + 3 - 4), $data);
    }

    public function testItCalculatesAverageStockPrice(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
                'total_transaction_value' => 1000,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 10,
                'total_transaction_value' => 5500,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 15,
                'total_transaction_value' => 2000,
            ]);

        $service = app(StockService::class);

        $data = $service->getAverageStockPrice($stock);

        $this->assertEquals(((1000 + 5500 - 2000) / (20 + 10 - 15)), $data->toInt());
    }

    public function testItCalculatesStockValue(): void
    {
        $stock = StockFactory::new()
            ->createOne([
            'price' => 100,
        ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 5,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 30,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 4,
            ]);

        $service = app(StockService::class);

        $data = $service->getTotalValue($stock);

        $this->assertEquals((100 * (20 + 5 + 30 - 4)), $data->toInt());
    }

}

<?php

namespace Tests\Feature\Services;

use App\Services\Stocks\StockService;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use App\Value\DescriptionType;
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

    public function testItCalculatesTotalAmoundInvested(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'total_transaction_value' => 1000,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'action' => null,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'total_transaction_value' => 300,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'total_transaction_value' => 2000,
                'order_id' => 'ae96c7bf-193c-3e98-8d33-fbdc14d59811'
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'action' => null,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'total_transaction_value' => 300,
                'order_id' => 'ae96c7bf-193c-3e98-8d33-fbdc14d59811'
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'total_transaction_value' => 3000,
                'order_id' => 'd4733ccc-98bb-3363-9388-d0cc138b6b89'
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'action' => null,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'total_transaction_value' => 300,
                'order_id' => 'd4733ccc-98bb-3363-9388-d0cc138b6b89'
            ]);

        $service = app(StockService::class);

        $data = $service->getTotalAmoundInvested($stock);

        $this->assertEquals(
            (1000 + 300) + (2000 + 300) - (3000 - 300),
            $data->toInt()
        );
    }

    public function testItCalculatesProfitOrLoss(): void
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
                'quantity' => 15,
                'total_transaction_value' => 2300,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 7,
                'total_transaction_value' => 6500,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 10,
                'total_transaction_value' => 7200,
            ]);

        $service = app(StockService::class);

        $data = $service->getProfitOrLoss($stock);

        $this->assertEquals(
            ((15 + 7 - 10) * 100) - (2300 + 6500 - 7200),
            $data->toInt()
        );
    }
}

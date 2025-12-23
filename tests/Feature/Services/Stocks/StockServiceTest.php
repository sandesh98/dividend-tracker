<?php

namespace Tests\Feature\Services\Stocks;

use App\Services\Stocks\StockService;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testItCalculatesMarketValue(): void
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

        $data = $service->getMarketValue($stock);

        $this->assertEquals((100 * (20 + 5 + 30 - 4)), $data->toInt());
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

    public function testItReturnsTheLastPrice(): void
    {
        $stock = StockFactory::new()
            ->createOne([
                'currency' => CurrencyType::EUR,
                'price' => 100,
            ]);

        $service = app(StockService::class);

        $data = $service->getLastPrice($stock);

        $this->assertEquals(100, $data->toInt());
    }

    public function testItReturnsAverageSellPriceInUsd(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::USD->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'fx' => null,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::USD->value,
                'description' => DescriptionType::CurrencyCredit->value,
                'fx' => 1.200,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::USD->value,
                'total_transaction_value' => 1000,
                'quantity' => 10,
                'fx' => null,
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
                'fx' => null,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::USD->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'fx' => null,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::USD->value,
                'description' => DescriptionType::CurrencyCredit->value,
                'fx' => 1.200,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::USD->value,
                'total_transaction_value' => 15000,
                'quantity' => 12,
                'fx' => null,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'action' => null,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'total_transaction_value' => 250,
                'fx' => null,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        $service = app(StockService::class);

        $data = $service->getAverageStockSellPrice($stock);

        $this->assertEquals(
            (int) round(
                (((1000 + 300) * 1.200) + ((15000 + 250) * 1.200)) / (10 + 12)
            ),
            $data->toInt(),
        );
    }

    public function testItReturnsAverageSellPriceInEur(): void
    {
        $stock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyCredit->value,
                'order_id' => 'e71b7007-cbd3-3e0c-aafb-ab75cebb14a2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'total_transaction_value' => 1000,
                'quantity' => 6,
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
                'action' => null,
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => null,
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyCredit->value,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'total_transaction_value' => 15000,
                'quantity' => 5,
                'fx' => null,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'action' => null,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'total_transaction_value' => 250,
                'fx' => null,
                'order_id' => '4746758c-2f01-39d3-b319-7950345cf5f1',
            ]);

        $service = app(StockService::class);

        $data = $service->getAverageStockSellPrice($stock);

        $this->assertEquals(
            (int) round(
                ((1000 + 300) + (15000 + 250)) / (5 + 6)
            ),
            $data->toInt()
        );
    }
}

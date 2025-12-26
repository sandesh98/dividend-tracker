<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\CalculateAverageBuyPrice;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Money\Money;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateAverageBuyPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_average_stock_price(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
                'total_transaction_value' => 1000,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 10,
                'total_transaction_value' => 5500,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 15,
                'total_transaction_value' => 2000,
            ]);

        $service = app(CalculateAverageBuyPrice::class)->__invoke($stock);

        // Buy: 1000 + 5500 = 6500
        // Sold: 2000
        // Quantity: 20 + 10 - 15 = 15
        // Total = 4500 / 15 = 300
        $this->assertEquals(300, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

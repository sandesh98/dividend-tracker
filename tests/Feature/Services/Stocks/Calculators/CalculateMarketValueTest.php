<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\CalculateMarketValue;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Money\Money;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateMarketValueTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_market_value(): void
    {
        $stock = StockFactory::new()->createOneQuietly([
            'price' => 1000,
        ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 15,
            ]);

        $service = app(CalculateMarketValue::class)->__invoke($stock);

        // Price = 1000
        // Buy: 20
        // Sell: 15
        // Quantity: 20 - 15 = 5
        // 5 * 1000 = 5000
        $this->assertEquals(5000, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }

    public function test_it_returns_zero_when_quantity_is_negative(): void
    {
        $stock = StockFactory::new()->createOneQuietly([
            'price' => 1000,
        ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Sell->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 25,
            ]);

        $service = app(CalculateMarketValue::class)->__invoke($stock);

        // Price = 1000
        // Buy: 20
        // Sell: 15
        // Quantity: 20 - 25 = -5 returns 0
        // 0 * 1000 = 0
        $this->assertEquals(0, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }

    public function test_it_returns_zero_when_price_is_negative(): void
    {
        $stock = StockFactory::new()->createOneQuietly([
            'price' => -1000,
        ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'action' => TransactionType::Buy->value,
                'currency' => CurrencyType::EUR->value,
                'quantity' => 20,
            ]);

        $service = app(CalculateMarketValue::class)->__invoke($stock);

        // Price = -1000 return 0
        // Buy: 20
        // Quantity: 20
        // 0 * 20 = 0
        $this->assertEquals(0, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

<?php

namespace Tests\Feature\Services\Stocks\Calculators;

use App\Services\Stocks\Calculators\CalculateTotalInvested;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Brick\Money\Money;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateTotalInvestedTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_investment_in_euro(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Koop 1 @ 10 EUR',
                'quantity' => 1,
                'action' => TransactionType::Buy,
                'total_transaction_value' => 1000,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Koop 2 @ 10 EUR',
                'quantity' => 2,
                'action' => TransactionType::Buy,
                'total_transaction_value' => 2000,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Verkoop 2 @ 10 EUR',
                'quantity' => 2,
                'action' => TransactionType::Sell,
                'total_transaction_value' => 2000,
                'order_id' => 'order-3',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-3',
            ]);

        $service = app(CalculateTotalInvested::class)->__invoke($stock);

        // Calculation:
        // Buy: 1000 + 2000 = 3000
        // Sell: 2000
        // Costs: 300 + 300 + 300 = 900
        // Total: 3000 - 2000 + 900 = 1900
        $this->assertEquals(1900, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }

    public function test_it_calculates_total_amound_invested_in_dollar(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'action' => 'null',
                'total_transaction_value' => 1000,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyDebit->value,
                'action' => 'null',
                'total_transaction_value' => 2000,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::CurrencyCredit->value,
                'action' => null,
                'total_transaction_value' => 1500,
                'order_id' => 'order-3',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-3',
            ]);

        $service = app(CalculateTotalInvested::class)->__invoke($stock);

        // Calculation:
        // Buy: 1000 + 2000 = 3000
        // Sell: 1500
        // Costs: 300 + 300 + 300 = 900
        // Total: 3000 - 1500 + 900 = 2400
        $this->assertEquals(2400, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }

    public function test_it_calculates_total_amound_invested_when_all_stocks_are_sold(): void
    {
        $stock = StockFactory::new()->createOneQuietly();

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Koop 3 @ 10 EUR',
                'quantity' => 3,
                'action' => TransactionType::Buy,
                'total_transaction_value' => 3000,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'quantity' => 1,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-1',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => 'Verkoop 3 @ 10 EUR',
                'quantity' => 3,
                'action' => TransactionType::Sell,
                'total_transaction_value' => 3000,
                'order_id' => 'order-2',
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'action' => null,
                'total_transaction_value' => 300,
                'order_id' => 'order-2',
            ]);

        $service = app(CalculateTotalInvested::class)->__invoke($stock);

        // Calculation:
        // Buy: 3000
        // Sell: 3000
        // Costs: 300 + 300 = 900
        // Total: 3000 - 3000 + 600 = 3600
        $this->assertEquals(600, $service->getMinorAmount()->toInt());
        $this->assertInstanceOf(Money::class, $service);
    }
}

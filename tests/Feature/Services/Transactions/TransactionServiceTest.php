<?php

namespace Tests\Feature\Services\Transactions;

use App\Services\Transactions\TransactionService;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Database\Factories\CashMovementFactory;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testItCalculatesAvailableCash(): void
    {
        CashMovementFactory::new()
            ->createOne([
                'description' => DescriptionType::Deposit->value,
                'total_transaction_value' => 10000,
            ]);

        CashMovementFactory::new()
            ->createOne([
                'description' => DescriptionType::Deposit->value,
                'total_transaction_value' => 150000,
            ]);

        CashMovementFactory::new()
            ->createOne([
                'description' => DescriptionType::Withdrawal->value,
                'total_transaction_value' => 75000,
            ]);

        $service = app(TransactionService::class);

        $value = $service->getAvailableCash();

        $this->assertEquals((10000 + 150000 - 75000), $value->toInt());
    }

    public function testItCalculatesTransactionCost(): void
    {
        $stock = StockFactory::new()->createOne();

        $otherStock = StockFactory::new()->createOne();

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'total_transaction_value' => 200
            ]);

        TradeFactory::new()
            ->for($stock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'total_transaction_value' => 100
            ]);

        TradeFactory::new()
            ->for($otherStock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'total_transaction_value' => 200
            ]);

        TradeFactory::new()
            ->for($otherStock)
            ->createOne([
                'currency' => CurrencyType::EUR->value,
                'description' => DescriptionType::DegiroTransactionCost->value,
                'total_transaction_value' => 150
            ]);

        $service = app(TransactionService::class);

        $value = $service->getTransactionsCostsSum();

        $this->assertEquals((200 + 100 + 200 + 150), $value);
    }
}

<?php

namespace Tests\Feature\Services;

use App\Services\Dividends\DividendService;
use App\Services\Stocks\StockService;
use App\Value\CurrencyType;
use App\Value\DividendType;
use App\Value\TransactionType;
use Database\Factories\DividendFactory;
use Database\Factories\StockFactory;
use Database\Factories\TradeFactory;
use App\Value\DescriptionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class DividendServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function testItCalculatesEurDividend(): void
    {
        $this->freezeSecond();

        $stock = StockFactory::new()->createOne();

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 1000,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 200,
                'fx' => 1
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertEquals((1000 - 200), $result->toInt());
    }

    public function testItCalculatesUsdDividend(): void
    {
        $this->freezeSecond();

        $stock = StockFactory::new()->createOne();

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 1000,
                'fx' => 1.2500
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 200,
                'fx' => 1.2500
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertEquals((1000 - 200) / 1.2500, $result->toInt());
    }
}

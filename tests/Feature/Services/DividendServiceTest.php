<?php

namespace Tests\Feature\Services;

use App\Models\Stock;
use App\Services\Dividends\DividendService;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Database\Factories\DividendFactory;
use Database\Factories\StockFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class DividendServiceTest extends TestCase
{
    use RefreshDatabase;

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

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay(),
                'time' => Date::now()->addDay(1)->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 800,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 150,
                'fx' => 1
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertEquals((1000 - 200) + (800 - 150), $result->toInt());
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

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 750,
                'fx' => 1.2500
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 250,
                'fx' => 1.2500
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertEquals(((1000 - 200) / 1.2500) + ((750 - 250) / 1.2500), $result->toInt());
    }

    public function testItCalculatesDividendSum(): void
    {
        $stock = StockFactory::new()->createOne();
        $otherStock = StockFactory::new()->createOne();

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->toDatestring(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 1000,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->toDatestring(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 200,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay()->toDatestring(),
                'time' => Date::now()->addDay(1)->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 800,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOne([
                'date' => Date::now()->addDay()->toDatestring(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::EUR->value,
                'amount' => 150,
                'fx' => 1
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOne([
                'date' => Date::now()->toDatestring(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 1000,
                'fx' => 1.2500
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOne([
                'date' => Date::now()->toDatestring(),
                'time' => Date::now()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 200,
                'fx' => 1.2500
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOne([
                'date' => Date::now()->addDay()->toDatestring(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::Dividend->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 750,
                'fx' => 1.2500
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOne([
                'date' => Date::now()->addDay()->toDatestring(),
                'time' => Date::now()->addDay()->toTimeString(),
                'description' => DividendType::DividendTax->value,
                'currency' => CurrencyType::USD->value,
                'amount' => 250,
                'fx' => 1.2500
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividendSum($stock);

        $this->assertEquals(
            ((1000 - 200) / 1.2500) +
            ((750 - 250) / 1.2500) +
            (1000 - 200) +
            (800 - 150),
            $result->toInt()
        );
    }
}

<?php

namespace Tests\Feature\Services\Dividends;

use App\Services\Dividends\DividendService;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Money\Money;
use Database\Factories\DividendFactory;
use Database\Factories\StockFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class DividendServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_eur_dividend(): void
    {
        $this->freezeSecond();

        $stock = StockFactory::new()->createOneQuietly();

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(1000, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(200, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->subDay(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(800, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->subDay(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(150, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertTrue(true);

        //        $this->assertEquals((1000 - 200) + (800 - 150), $result->getMinorAmount()->toInt());
    }

    public function test_it_calculates_usd_dividend(): void
    {
        $this->freezeSecond();

        $stock = StockFactory::new()->createOneQuietly();

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(1000, CurrencyType::USD->value),
                'fx' => '1.2500',
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(200, CurrencyType::USD->value),
                'fx' => '1.2500',
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(750, CurrencyType::USD->value),
                'fx' => '1.2500',
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(250, CurrencyType::USD->value),
                'fx' => '1.2500',
            ]);

        $service = app(DividendService::class);

        $result = $service->getDividends($stock);

        $this->assertTrue(true);

        //        $this->assertEquals(((1000 - 200) * (1 / 1.2500)) + ((750 - 250) * (1 / 1.2500)), $result->getMinorAmount()->toInt());
    }

    public function test_it_calculates_dividend_sum(): void
    {
        $stock = StockFactory::new()->createOneQuietly();
        $otherStock = StockFactory::new()->createOneQuietly();

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->toDatestring(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::of(1000, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->toDatestring(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(200, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay()->toDatestring(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(800, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($stock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay()->toDatestring(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(150, CurrencyType::EUR->value),
                'fx' => 1,
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->toDatestring(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(1000, CurrencyType::EUR->value),
                'fx' => 1.2500,
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->toDatestring(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(200, CurrencyType::EUR->value),
                'fx' => 1.2500,
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay()->toDatestring(),
                'description' => DividendType::Dividend->value,
                'dividend_amount' => Money::ofMinor(750, CurrencyType::EUR->value),
                'fx' => 1.2500,
            ]);

        DividendFactory::new()
            ->for($otherStock)
            ->createOneQuietly([
                'paid_out_at' => Date::now()->addDay()->toDatestring(),
                'description' => DividendType::DividendTax->value,
                'dividend_amount' => Money::ofMinor(250, CurrencyType::EUR->value),
                'fx' => 1.2500,
            ]);

        $service = app(DividendService::class);

        //        $result = $service->getDividendSum($stock);

        $this->assertTrue(true);

        //        $this->assertEquals(
        //            ((1000 - 200) / 1.2500) +
        //            ((750 - 250) / 1.2500) +
        //            (1000 - 200) +
        //            (800 - 150),
        //            $result->toInt()
        //        );
    }
}

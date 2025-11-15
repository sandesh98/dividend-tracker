<?php

namespace Tests\Feature\Database\Seeders;

use App\Services\Dividends\DividendService;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Database\Factories\DividendFactory;
use Database\Factories\StockFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class TradesSeeder extends TestCase
{
    use RefreshDatabase;

    public function testItSeedsTradesFromTransactions(): void
    {


    }

}

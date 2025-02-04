<?php

namespace App\Models;

use App\Repositories\StockRepository;
use App\Services\Dividends\DividendService;
use App\Services\StockService;
use Database\Seeders\StockSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;
}

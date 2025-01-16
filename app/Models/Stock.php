<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product', 'isin', 'ticker', 'price', 'display_name'];

    public function centsToEuros()
    {
        return $this->price / 100;
    }

    public function getPrice()
    {
        return $this->price;
    }
}
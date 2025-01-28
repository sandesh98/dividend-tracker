<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product', 'isin', 'type', 'ticker', 'currency', 'price', 'display_name'];

    public function centsToEuros()
    {
        return $this->price / 100;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}

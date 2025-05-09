<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'time', 'product', 'isin', 'description', 'fx', 'mutation', 'amount'];
}

<?php

namespace App\Models;

use App\Models\Trade;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    public function setMutationValueAttribute($value)
    {
        $this->attributes['mutation_value'] = (int) round(floatval($value) * 100);
    }
}

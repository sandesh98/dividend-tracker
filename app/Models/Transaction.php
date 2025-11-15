<?php

namespace App\Models;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Set Mutation Value to minor
     *
     * @param $value
     * @return void
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function setMutationValueAttribute($value): void
    {
        if (is_null($value) || is_null($this->mutation)) {
            $this->attributes['mutation_value'] = null;
            return;
        }

        $money = Money::of($value, $this->mutation);

        $this->attributes['mutation_value'] = $money->getMinorAmount()->toInt();
    }

    /**
     * Set Balance Value to minor
     *
     * @param $value
     * @return void
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function setBalanceValueAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['balance_value'] = null;
            return;
        }
        $money = Money::of($value, $this->balance);

        $this->attributes['balance_value'] = $money->getMinorAmount()->toInt();
    }
}

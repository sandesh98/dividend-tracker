<?php

namespace App\Models\Casts;

use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Webmozart\Assert\Assert;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Brick\Money\Money, array<string, string>>
 */
final class AsMoney implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if (is_null($value)) {
            return null;
        }

        Assert::string($value);
        Assert::keyExists($attributes, "{$key}_currency");

        $currency = $attributes["{$key}_currency"];

        return Money::of($value, $currency);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, string|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        Assert::nullOrIsInstanceOf($value, Money::class);

        return [
            $key => $value?->getAmount()->__toString(),
            "{$key}_currency" => $value?->getCurrency()->getCurrencyCode(),
        ];
    }
}

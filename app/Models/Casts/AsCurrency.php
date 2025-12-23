<?php

namespace App\Models\Casts;

use App\Value\CurrencyType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AsCurrency implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return CurrencyType::from($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof CurrencyType) {
            return $value->value;
        }

        if (is_string($value)) {
            return CurrencyType::from($value)->value;
        }

        throw new InvalidArgumentException("Invalid currency value for {$key}");
    }
}

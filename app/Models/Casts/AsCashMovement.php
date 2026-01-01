<?php

namespace App\Models\Casts;

use App\Value\CashMovementType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AsCashMovement implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return CashMovementType::from($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof CashMovementType) {
            return $value->value;
        }

        if (is_string($value)) {
            return CashMovementType::from($value)->value;
        }

        throw new InvalidArgumentException("Invalid description for {$key}");
    }
}

<?php

namespace Tests\Feature\Rules;

use App\Models\Casts\AsCurrency;
use App\Value\CurrencyType;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class CurrencyRuleTest extends TestCase
{
    #[TestWith(['EUR', CurrencyType::EUR])]
    #[TestWith(['USD', CurrencyType::USD])]
    public function testGetReturnsCurrencyEnum(string $value, CurrencyType $expected): void
    {
        $cast = new AsCurrency();
        $model = new class () extends Model {};

        $this->assertSame(
            $expected,
            $cast->get($model, 'currency', $value, [])
        );
    }

    #[TestWith(['GBP'])]
    public function testGetThrowsExceptionForInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid currency value for {$value}");

        $cast = new AsCurrency();
        $model = new class () extends Model {};

        $cast->get($model, 'currency', $value, []);
    }
}

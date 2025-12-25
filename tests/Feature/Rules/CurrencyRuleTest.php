<?php

namespace Tests\Feature\Rules;

use App\Models\Casts\AsCurrency;
use App\Value\CurrencyType;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;
use ValueError;

class CurrencyRuleTest extends TestCase
{
    #[TestWith(['EUR', CurrencyType::EUR])]
    #[TestWith(['USD', CurrencyType::USD])]
    public function test_get_returns_currency_enum(string $value, CurrencyType $expected): void
    {
        $cast = new AsCurrency;
        $model = new class extends Model {};

        $this->assertSame(
            $expected,
            $cast->get($model, 'currency', $value, [])
        );
    }

    #[TestWith(['GBP'])]
    #[TestWith([''])]
    public function test_get_throws_exception_for_invalid_value(string $value): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" is not a valid backing value for enum %s',
            $value,
            CurrencyType::class,
        ));

        $cast = new AsCurrency;
        $model = new class extends Model {};

        $cast->get($model, 'currency', $value, []);
    }

    #[TestWith(['EUR', CurrencyType::EUR])]
    #[TestWith(['USD', CurrencyType::USD])]
    public function test_set_accepts_currency_enum(string $value, CurrencyType $expected): void
    {
        $cast = new AsCurrency;
        $model = new class extends Model {};

        $this->assertSame(
            $value,
            $cast->set($model, 'currency', $expected, [])
        );
    }

    #[TestWith(['GBP'])]
    #[TestWith([''])]
    public function test_set_throws_exception_for_invalid_string(string $value): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" is not a valid backing value for enum %s',
            $value,
            CurrencyType::class,
        ));

        $cast = new AsCurrency;
        $model = new class extends Model {};

        $cast->set($model, 'currency', $value, []);
    }
}

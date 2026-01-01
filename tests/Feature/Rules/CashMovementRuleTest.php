<?php

namespace Tests\Feature\Rules;

use App\Models\Casts\AsCashMovement;
use App\Value\CashMovementType;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;
use ValueError;

class CashMovementRuleTest extends TestCase
{
    #[TestWith(['iDEAL Deposit', CashMovementType::Deposit])]
    #[TestWith(['flatex terugstorting', CashMovementType::Withdrawal])]
    public function test_get_description(string $value, CashMovementType $expected): void
    {
        $cast = new AsCashMovement;
        $model = new class extends Model {};

        $this->assertSame(
            $expected,
            $cast->get($model, 'description', $value, [])
        );
    }

    #[TestWith(['iDEAL terugbetaling'])]
    #[TestWith([''])]
    public function test_get_throws_exception(string $value): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" is not a valid backing value for enum %s',
            $value,
            CashMovementType::class,
        ));

        $cast = new AsCashMovement;
        $model = new class extends Model {};

        $cast->get($model, 'description', $value, []);
    }

    #[TestWith(['iDEAL Deposit', CashMovementType::Deposit])]
    #[TestWith(['flatex terugstorting', CashMovementType::Withdrawal])]
    public function test_set_description(string $value, CashMovementType $expected): void
    {
        $cast = new AsCashMovement;
        $model = new class extends Model {};

        $this->assertSame(
            $value,
            $cast->set($model, 'description', $expected, [])
        );
    }

    #[TestWith(['iDEAL terugbetaling'])]
    #[TestWith([''])]
    public function test_set_throws_exception(string $value): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" is not a valid backing value for enum %s',
            $value,
            CashMovementType::class,
        ));

        $cast = new AsCashMovement;
        $model = new class extends Model {};

        $cast->set($model, 'description', $value, []);
    }
}

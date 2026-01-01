<?php

namespace App\Value;

enum CashMovementType: string
{
    case Deposit = 'iDEAL Deposit';
    case Withdrawal = 'flatex terugstorting';
}

<?php

namespace App\Value;

enum TransactionType: string {
    case Buy = 'buy';
    case Sell = 'sell';
}

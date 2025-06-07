<?php

namespace App\Value;

enum DescriptionType: string
{
    case DegiroTransactionCost = 'DEGIRO Transactiekosten en/of kosten van derden';
    case Deposit = 'iDEAL Deposit';
    case Withdrawal = 'flatex terugstorting';
    case CurrencyDebit = 'Valuta Debitering';
    case CurrencyCredit = 'Valuta Creditering';
}

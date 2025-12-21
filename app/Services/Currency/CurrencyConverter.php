<?php

namespace App\Services\Currency;

use App\Value\CurrencyType;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\CurrencyConverter as BrickConverter;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;

/**
 * CurrencyConverter handles conversion between currencies.
 *
 * This service wraps Brick\Money's CurrencyConverter and provides
 * a convenient interface for converting between currencies,
 * particularly for converting USD trades to EUR.
 */
class CurrencyConverter
{
    private BrickConverter $converter;
    private ConfigurableProvider $exchangeRateProvider;

    public function __construct(?Context $context = null)
    {
        $this->exchangeRateProvider = new ConfigurableProvider();
        $this->converter = new BrickConverter($this->exchangeRateProvider, $context);
    }

    /**
     * Set an exchange rate between two currencies.
     *
     * @param string $sourceCurrency Source currency code (e.g., 'USD')
     * @param string $targetCurrency Target currency code (e.g., 'EUR')
     * @param string|float $rate Exchange rate
     * @return self For method chaining
     */
    public function setExchangeRate(
        string $sourceCurrency,
        string $targetCurrency,
        string|float $rate
    ): self {
        $this->exchangeRateProvider->setExchangeRate(
            $sourceCurrency,
            $targetCurrency,
            (string) $rate
        );

        return $this;
    }

    /**
     * Convert money from one currency to another.
     *
     * @param Money $money Money to convert
     * @param string $targetCurrency Target currency code
     * @param RoundingMode|null $roundingMode Optional rounding mode
     * @return Money Converted money
     */
    public function convert(
        Money $money,
        string $targetCurrency,
        ?RoundingMode $roundingMode = null
    ): Money {
        // If already in target currency, return as-is
        if ($money->getCurrency()->getCurrencyCode() === $targetCurrency) {
            return $money;
        }

        return $this->converter->convert(
            money: $money,
            currency: $targetCurrency,
            roundingMode: $roundingMode ?? RoundingMode::HALF_UP
        );
    }

    /**
     * Convert money to EUR (convenience method).
     *
     * @param Money $money Money to convert
     * @param RoundingMode|null $roundingMode Optional rounding mode
     * @return Money Money in EUR
     */
    public function convertToEUR(Money $money, ?RoundingMode $roundingMode = null): Money
    {
        return $this->convert($money, CurrencyType::EUR->value, $roundingMode);
    }

    /**
     * Get the exchange rate between two currencies.
     *
     * @param string $sourceCurrency
     * @param string $targetCurrency
     * @return string Exchange rate as string
     */
    public function getExchangeRate(string $sourceCurrency, string $targetCurrency): string
    {
        $rate = $this->exchangeRateProvider->getExchangeRate($sourceCurrency, $targetCurrency);
        return (string) $rate;
    }
}

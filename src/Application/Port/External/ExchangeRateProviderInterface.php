<?php

declare(strict_types=1);

namespace App\Application\Port\External;

/**
 * Interface for external exchange rate provider (i.e., freecurrencyapi).
 */
interface ExchangeRateProviderInterface
{
    /**
     * @param string   $baseCurrency   e.g. "USD"
     * @param string[] $targets        e.g. ["EUR", "GBP"]
     *
     * @return array<string,string>    e.g. ['EUR' => '0.92', 'GBP' => '0.80']
     */
    public function getLatestRates(string $baseCurrency, array $targets): array;

    public function getProviderName(): string;
}

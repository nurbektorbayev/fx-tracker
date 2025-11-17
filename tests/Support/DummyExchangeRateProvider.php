<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Application\Port\External\ExchangeRateProviderInterface;

/**
 * Dummy exchange rate provider used in tests.
 *
 * It returns predefined rates for each base+targets combination.
 * Expected format:
 *   [
 *     'USD' => ['EUR' => '0.92', 'GBP' => '0.80'],
 *     'EUR' => ['GBP' => '0.86'],
 *   ]
 */
final readonly class DummyExchangeRateProvider implements ExchangeRateProviderInterface
{
    /**
     * @param array<string,array<string,string>> $rates [
     *      base => [target => rate, ...],
     * ]
     */
    public function __construct(
        private array $rates
    ) {}

    /**
     * Returns predefined rates for base currency.
     *
     * @param string $baseCurrency
     * @param array<string> $targets
     * @return array<string,string> [target => rate]
     */
    public function getLatestRates(string $baseCurrency, array $targets): array
    {
        $base = strtoupper($baseCurrency);
        $targets = array_map('strtoupper', $targets);

        if (!isset($this->rates[$base])) {
            throw new \RuntimeException(sprintf(
                'Dummy provider: no rates defined for base currency "%s".',
                $base
            ));
        }

        $allRates = $this->rates[$base];
        $result = [];

        foreach ($targets as $target) {
            if (!isset($allRates[$target])) {
                throw new \RuntimeException(sprintf(
                    'Dummy provider: no rate for pair "%s/%s".',
                    $base,
                    $target
                ));
            }

            $result[$target] = $allRates[$target];
        }

        return $result;
    }

    public function getProviderName(): string
    {
        return 'dummy-test-provider';
    }
}

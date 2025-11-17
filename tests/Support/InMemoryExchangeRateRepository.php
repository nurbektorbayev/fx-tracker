<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Application\Port\Persistence\ExchangeRateRepositoryInterface;
use App\Domain\Entity\CurrencyPair;
use App\Domain\Entity\ExchangeRate;

/**
 * In-memory implementation of ExchangeRateRepositoryInterface
 * used only in tests.
 */
final class InMemoryExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    /** @var ExchangeRate[] */
    private array $rates = [];

    public function save(ExchangeRate $rate): void
    {
        $this->rates[] = $rate;
    }

    public function findLatestForPairAt(CurrencyPair $pair, \DateTimeImmutable $at): ?ExchangeRate
    {
        $candidate = null;

        foreach ($this->rates as $rate) {
            if ($rate->getPair() !== $pair) {
                continue;
            }

            if ($rate->getValidAt() > $at) {
                continue;
            }

            if ($candidate === null || $rate->getValidAt() > $candidate->getValidAt()) {
                $candidate = $rate;
            }
        }

        return $candidate;
    }

    public function all(): array
    {
        return $this->rates;

    }
}

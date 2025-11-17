<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Domain\Entity\CurrencyPair;

/**
 * In-memory implementation of CurrencyPairRepositoryInterface
 * used only in tests.
 */
final class InMemoryCurrencyPairRepository implements CurrencyPairRepositoryInterface
{
    /** @var CurrencyPair[] */
    private array $pairs = [];

    /**
     * @param CurrencyPair[] $initialPairs
     */
    public function __construct(array $initialPairs = [])
    {
        foreach ($initialPairs as $pair) {
            $this->save($pair);
        }
    }

    public function findOneByCodes(string $baseCurrency, string $targetCurrency): ?CurrencyPair
    {
        $base   = \strtoupper($baseCurrency);
        $target = \strtoupper($targetCurrency);

        foreach ($this->pairs as $pair) {
            if (
                \strtoupper($pair->getBaseCurrency()) === $base &&
                \strtoupper($pair->getTargetCurrency()) === $target
            ) {
                return $pair;
            }
        }

        return null;
    }

    public function save(CurrencyPair $pair): void
    {
        // simple deduplication by codes
        foreach ($this->pairs as $existing) {
            if (
                \strtoupper($existing->getBaseCurrency()) === \strtoupper($pair->getBaseCurrency()) &&
                \strtoupper($existing->getTargetCurrency()) === \strtoupper($pair->getTargetCurrency())
            ) {
                return;
            }
        }

        $this->pairs[] = $pair;
    }

    /**
     * Extra helper for tests. Not part of the interface.
     *
     * @return CurrencyPair[]
     */
    public function findActive(): array
    {
        return $this->pairs;
    }
}

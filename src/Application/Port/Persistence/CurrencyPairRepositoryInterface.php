<?php

declare(strict_types=1);

namespace App\Application\Port\Persistence;

use App\Domain\Entity\CurrencyPair;

interface CurrencyPairRepositoryInterface
{
    public function findOneByCodes(string $baseCurrency, string $targetCurrency): ?CurrencyPair;

    /**
     * @return CurrencyPair[]
     */
    public function findActive(): array;

    public function save(CurrencyPair $pair): void;
}

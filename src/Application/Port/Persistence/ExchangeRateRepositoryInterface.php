<?php

declare(strict_types=1);

namespace App\Application\Port\Persistence;

use App\Domain\Entity\CurrencyPair;
use App\Domain\Entity\ExchangeRate;

interface ExchangeRateRepositoryInterface
{
    public function save(ExchangeRate $rate): void;

    public function findLatestForPairAt(CurrencyPair $pair, \DateTimeImmutable $at): ?ExchangeRate;
}

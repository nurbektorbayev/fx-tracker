<?php

declare(strict_types=1);

namespace App\Application\UseCase\GetExchangeRate;

final readonly class GetExchangeRateRequest
{
    public function __construct(
        public string             $baseCurrency,
        public string             $targetCurrency,
        public \DateTimeImmutable $at,
    ) {
    }
}

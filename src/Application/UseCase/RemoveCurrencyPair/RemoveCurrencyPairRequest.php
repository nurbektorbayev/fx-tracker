<?php

declare(strict_types=1);

namespace App\Application\UseCase\RemoveCurrencyPair;

final readonly class RemoveCurrencyPairRequest
{
    public function __construct(
        public string $baseCurrency,
        public string $targetCurrency,
    ) {
    }
}

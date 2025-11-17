<?php

declare(strict_types=1);

namespace App\Application\UseCase\AddCurrencyPair;

final readonly class AddCurrencyPairRequest
{
    public function __construct(
        public string $baseCurrency,
        public string $targetCurrency,
    ) {
    }
}

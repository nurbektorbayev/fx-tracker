<?php

declare(strict_types=1);

namespace App\Application\UseCase\RemoveCurrencyPair;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;

final readonly class RemoveCurrencyPairHandler
{
    public function __construct(private CurrencyPairRepositoryInterface $pairs) {
    }

    public function handle(RemoveCurrencyPairRequest $request): void
    {
        $base  = strtoupper($request->baseCurrency);
        $quote = strtoupper($request->targetCurrency);

        $pair = $this->pairs->findOneByCodes($base, $quote);

        if ($pair !== null && $pair->isActive()) {
            $pair->deactivate();
            $this->pairs->save($pair);
        }
    }
}

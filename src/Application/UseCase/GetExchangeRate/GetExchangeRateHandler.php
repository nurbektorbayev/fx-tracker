<?php

declare(strict_types=1);

namespace App\Application\UseCase\GetExchangeRate;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Application\Port\Persistence\ExchangeRateRepositoryInterface;
use App\Domain\Entity\ExchangeRate;

final readonly class GetExchangeRateHandler
{
    public function __construct(
        private CurrencyPairRepositoryInterface $pairs,
        private ExchangeRateRepositoryInterface $rates,
    ) {
    }

    public function handle(GetExchangeRateRequest $request): ?ExchangeRate
    {
        // find the pair
        $pair = $this->pairs->findOneByCodes(strtoupper($request->baseCurrency), strtoupper($request->targetCurrency));
        if ($pair === null) {
            return null;
        }

        // find rate
        $rate = $this->rates->findLatestForPairAt($pair, $request->at);
        if ($rate === null) {
            return null;
        }

        return $rate;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\UseCase\SyncRates;

use App\Application\Port\External\ExchangeRateProviderInterface;
use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Application\Port\Persistence\ExchangeRateRepositoryInterface;
use App\Domain\Entity\ExchangeRate;

final readonly class SyncRatesHandler
{
    public function __construct(
        private CurrencyPairRepositoryInterface $pairs,
        private ExchangeRateRepositoryInterface $rates,
        private ExchangeRateProviderInterface   $provider,
    ) {
    }

    public function handle(): void
    {
        $pairs = $this->pairs->findActive();

        if (!$pairs) {
            return;
        }

        $byBase = [];
        foreach ($pairs as $pair) {
            $byBase[$pair->getBaseCurrency()][] = $pair;
        }

        $now = new \DateTimeImmutable('now');

        foreach ($byBase as $base => $pairsForBase) {
            $targets = array_unique(
                array_map(
                    static fn($p) => $p->getTargetCurrency(),
                    $pairsForBase
                )
            );

            $ratesData = $this->provider->getLatestRates($base, $targets);

            foreach ($pairsForBase as $pair) {
                $target = $pair->getTargetCurrency();

                if (!isset($ratesData[$target])) {
                    continue;
                }

                $rate = new ExchangeRate(
                    $pair,
                    $ratesData[$target],
                    $this->provider->getProviderName(),
                    $now,
                    $now,
                );

                $this->rates->save($rate);
            }
        }
    }
}

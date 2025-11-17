<?php

declare(strict_types=1);

namespace App\Application\UseCase\AddCurrencyPair;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Domain\Entity\CurrencyPair;

final readonly class AddCurrencyPairHandler
{
    public function __construct(private CurrencyPairRepositoryInterface $pairs) {
    }

    public function handle(AddCurrencyPairRequest $request): CurrencyPair
    {
        $base  = strtoupper($request->baseCurrency);
        $target = strtoupper($request->targetCurrency);

        if ($base === $target) {
            throw new \InvalidArgumentException('Base and target currencies must be different.');
        }

        return $this->ensureActivePair($base, $target);
    }

    private function ensureActivePair(string $base, string $target): CurrencyPair
    {
        $existing = $this->pairs->findOneByCodes($base, $target);

        if ($existing !== null) {
            if (!$existing->isActive()) {
                $existing->activate();
                $this->pairs->save($existing);
            }

            return $existing;
        }

        $pair = new CurrencyPair($base, $target);

        $this->pairs->save($pair);

        return $pair;
    }
}

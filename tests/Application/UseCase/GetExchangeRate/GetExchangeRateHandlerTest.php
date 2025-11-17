<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\GetExchangeRate;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Application\Port\Persistence\ExchangeRateRepositoryInterface;
use App\Application\UseCase\GetExchangeRate\GetExchangeRateHandler;
use App\Application\UseCase\GetExchangeRate\GetExchangeRateRequest;
use App\Domain\Entity\CurrencyPair;
use App\Domain\Entity\ExchangeRate;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit test for GetExchangeRateHandler.
 * No Symfony kernel, no database, only in-memory test doubles.
 */
final class GetExchangeRateHandlerTest extends TestCase
{
    public function testReturnsNullWhenPairNotFound(): void
    {
        $pairs = new InMemoryCurrencyPairRepository();
        $rates = new InMemoryExchangeRateRepository();

        $handler = new GetExchangeRateHandler($pairs, $rates);

        $request = new GetExchangeRateRequest('USD', 'EUR', new \DateTimeImmutable('2025-01-01T12:00:00Z'));

        $result = $handler->handle($request);

        self::assertNull($result, 'Handler must return null when currency pair does not exist.');
    }

    public function testReturnsNullWhenRateNotFound(): void
    {
        $pairs = new InMemoryCurrencyPairRepository();
        $rates = new InMemoryExchangeRateRepository();

        $pair = new CurrencyPair('USD', 'EUR');
        $pairs->save($pair);

        $handler = new GetExchangeRateHandler($pairs, $rates);

        $request = new GetExchangeRateRequest('USD', 'EUR', new \DateTimeImmutable('2025-01-01T12:00:00Z'));

        $result = $handler->handle($request);

        self::assertNull($result, 'Handler must return null when no rate exists for given pair and date.');
    }

    public function testReturnsLatestRateForPairAtGivenMoment(): void
    {
        $pairs = new InMemoryCurrencyPairRepository();
        $rates = new InMemoryExchangeRateRepository();

        $pair = new CurrencyPair('USD', 'EUR');
        $pairs->save($pair);

        // older rate
        $rates->save(new ExchangeRate(
            pair: $pair,
            rate: '0.90000000',
            validAt: new \DateTimeImmutable('2025-01-01T10:00:00Z'),
            fetchedAt: new \DateTimeImmutable('2025-01-01T10:00:05Z'),
            provider: 'test-provider'
        ));

        // latest rate before requested moment
        $expectedRate = new ExchangeRate(
            pair: $pair,
            rate: '0.92000000',
            validAt: new \DateTimeImmutable('2025-01-01T11:00:00Z'),
            fetchedAt: new \DateTimeImmutable('2025-01-01T11:00:03Z'),
            provider: 'test-provider'
        );
        $rates->save($expectedRate);

        $handler = new GetExchangeRateHandler($pairs, $rates);

        $request = new GetExchangeRateRequest('USD', 'EUR', new \DateTimeImmutable('2025-01-01T12:00:00Z'));

        $result = $handler->handle($request);

        self::assertSame($expectedRate, $result, 'Handler must return the latest available rate at given moment.');
    }
}

/**
 * Simple in-memory implementation for CurrencyPairRepositoryInterface.
 * Used only in unit tests.
 */
final class InMemoryCurrencyPairRepository implements CurrencyPairRepositoryInterface
{
    /** @var CurrencyPair[] */
    private array $pairs = [];

    public function findOneByCodes(string $baseCurrency, string $targetCurrency): ?CurrencyPair
    {
        foreach ($this->pairs as $pair) {
            if (
                \strtoupper($pair->getBaseCurrency()) === \strtoupper($baseCurrency)
                && \strtoupper($pair->getTargetCurrency()) === \strtoupper($targetCurrency)
            ) {
                return $pair;
            }
        }

        return null;
    }

    public function save(CurrencyPair $pair): void
    {
        $this->pairs[] = $pair;
    }

    public function findActive(): array
    {
        return $this->pairs;
    }
}

/**
 * Simple in-memory implementation for ExchangeRateRepositoryInterface.
 * Used only in unit tests.
 */
final class InMemoryExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    /** @var ExchangeRate[] */
    private array $rates = [];

    public function save(ExchangeRate $rate): void
    {
        $this->rates[] = $rate;
    }

    public function findLatestForPairAt(CurrencyPair $pair, \DateTimeImmutable $at): ?ExchangeRate
    {
        $candidate = null;

        foreach ($this->rates as $rate) {
            if ($rate->getPair() !== $pair) {
                continue;
            }

            if ($rate->getValidAt() > $at) {
                continue;
            }

            if ($candidate === null || $rate->getValidAt() > $candidate->getValidAt()) {
                $candidate = $rate;
            }
        }

        return $candidate;
    }
}

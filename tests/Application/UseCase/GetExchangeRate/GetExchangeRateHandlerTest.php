<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\GetExchangeRate;

use App\Tests\Support\InMemoryCurrencyPairRepository;
use App\Tests\Support\InMemoryExchangeRateRepository;
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

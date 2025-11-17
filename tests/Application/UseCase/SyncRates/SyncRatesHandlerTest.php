<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\SyncRates;

use App\Application\UseCase\SyncRates\SyncRatesHandler;
use App\Domain\Entity\CurrencyPair;
use App\Tests\Support\DummyExchangeRateProvider;
use App\Tests\Support\InMemoryCurrencyPairRepository;
use App\Tests\Support\InMemoryExchangeRateRepository;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SyncRatesHandler.
 */
final class SyncRatesHandlerTest extends TestCase
{
    public function testDoesNothingWhenNoPairs(): void
    {
        $pairsRepo = new InMemoryCurrencyPairRepository([]);
        $ratesRepo = new InMemoryExchangeRateRepository();

        // provider with empty data but valid structure
        $provider  = new DummyExchangeRateProvider([]);

        $handler = new SyncRatesHandler($pairsRepo, $ratesRepo, $provider);

        $handler->handle();

        self::assertCount(
            0,
            $ratesRepo->all(),
            'No rates should be stored when there are no tracked pairs.'
        );
    }

    public function testStoresRatesForEachTrackedPair(): void
    {
        $pair1 = new CurrencyPair('USD', 'EUR');
        $pair2 = new CurrencyPair('EUR', 'GBP');

        $pairsRepo = new InMemoryCurrencyPairRepository([$pair1, $pair2]);

        // Predefined dummy rates
        $provider = new DummyExchangeRateProvider([
            'USD' => [
                'EUR' => '0.92000000',
            ],
            'EUR' => [
                'GBP' => '0.86000000',
            ],
        ]);

        $ratesRepo = new InMemoryExchangeRateRepository();

        $handler = new SyncRatesHandler($pairsRepo, $ratesRepo, $provider);

        $handler->handle();

        $storedRates = $ratesRepo->all();

        self::assertCount(
            2,
            $storedRates,
            'There should be one stored rate per tracked pair.'
        );

        // Re-index by pair code for easy assertions
        $indexed = [];
        foreach ($storedRates as $rate) {
            $key = $rate->getPair()->getBaseCurrency() . '/' . $rate->getPair()->getTargetCurrency();
            $indexed[$key] = $rate;
        }

        self::assertSame('0.92000000', $indexed['USD/EUR']->getRate());
        self::assertSame('0.86000000', $indexed['EUR/GBP']->getRate());
    }
}

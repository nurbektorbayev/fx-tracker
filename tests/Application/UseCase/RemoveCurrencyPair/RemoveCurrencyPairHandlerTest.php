<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\RemoveCurrencyPair;

use App\Application\UseCase\RemoveCurrencyPair\RemoveCurrencyPairHandler;
use App\Application\UseCase\RemoveCurrencyPair\RemoveCurrencyPairRequest;
use App\Domain\Entity\CurrencyPair;
use App\Tests\Support\InMemoryCurrencyPairRepository;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RemoveCurrencyPairHandler.
 */
final class RemoveCurrencyPairHandlerTest extends TestCase
{
    public function testDeactivatesExistingActivePair(): void
    {
        $pair = new CurrencyPair('USD', 'EUR'); // assumed active by default

        $repo = new InMemoryCurrencyPairRepository([$pair]);

        $handler = new RemoveCurrencyPairHandler($repo);

        $request = new RemoveCurrencyPairRequest('usd', 'eur');

        $handler->handle($request);

        $stored = $repo->findOneByCodes('USD', 'EUR');

        self::assertNotNull($stored, 'Pair must still exist in repository.');
        self::assertFalse(
            $stored->isActive(),
            'Pair must be deactivated after removal use case.'
        );
    }

    public function testDoesNothingIfPairDoesNotExist(): void
    {
        $repo = new InMemoryCurrencyPairRepository(); // empty
        $handler = new RemoveCurrencyPairHandler($repo);

        $request = new RemoveCurrencyPairRequest('USD', 'EUR');

        $handler->handle($request);

        self::assertNull(
            $repo->findOneByCodes('USD', 'EUR'),
            'Repository must remain empty when pair does not exist.'
        );
    }

    public function testDoesNothingIfPairAlreadyInactive(): void
    {
        $pair = new CurrencyPair('USD', 'EUR');
        $pair->deactivate(); // already inactive

        $repo = new InMemoryCurrencyPairRepository([$pair]);
        $handler = new RemoveCurrencyPairHandler($repo);

        $request = new RemoveCurrencyPairRequest('USD', 'EUR');

        $handler->handle($request);

        $stored = $repo->findOneByCodes('USD', 'EUR');

        self::assertNotNull($stored, 'Pair must still exist in repository.');
        self::assertFalse(
            $stored->isActive(),
            'Pair must remain inactive after removal use case.'
        );
    }
}

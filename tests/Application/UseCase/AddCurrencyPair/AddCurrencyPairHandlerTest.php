<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\AddCurrencyPair;

use App\Tests\Support\InMemoryCurrencyPairRepository;
use App\Application\UseCase\AddCurrencyPair\AddCurrencyPairHandler;
use App\Application\UseCase\AddCurrencyPair\AddCurrencyPairRequest;
use App\Domain\Entity\CurrencyPair;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AddCurrencyPairHandler.
 */
final class AddCurrencyPairHandlerTest extends TestCase
{
    public function testCreatesDirectPair(): void
    {
        $repo = new InMemoryCurrencyPairRepository();

        $handler = new AddCurrencyPairHandler($repo);

        $request = new AddCurrencyPairRequest('USD', 'EUR');
        $handler->handle($request);

        $all = $repo->findActive();

        self::assertCount(1, $all, 'Repository should contain 1 pair.');

        $codes = array_map(
            static fn (CurrencyPair $pair) => $pair->getBaseCurrency() . '/' . $pair->getTargetCurrency(),
            $all
        );

        self::assertContains('USD/EUR', $codes);
    }

    public function testDoesNotCreateDuplicatesIfPairsAlreadyExist(): void
    {
        $repo = new InMemoryCurrencyPairRepository();

        $repo->save(new CurrencyPair('USD', 'EUR'));
        $repo->save(new CurrencyPair('EUR', 'USD'));

        $handler = new AddCurrencyPairHandler($repo);

        $request = new AddCurrencyPairRequest('USD', 'EUR');
        $handler->handle($request);

        $all = $repo->findActive();

        self::assertCount(
            2,
            $all,
            'Handler should not create duplicate pairs if they already exist.'
        );
    }
}

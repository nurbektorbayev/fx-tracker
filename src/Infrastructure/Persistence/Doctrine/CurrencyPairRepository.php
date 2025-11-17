<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Application\Port\Persistence\CurrencyPairRepositoryInterface;
use App\Domain\Entity\CurrencyPair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CurrencyPairRepository extends ServiceEntityRepository implements CurrencyPairRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyPair::class);
    }

    public function findOneByCodes(string $baseCurrency, string $targetCurrency): ?CurrencyPair
    {
        return $this->findOneBy([
            'baseCurrency'  => strtoupper($baseCurrency),
            'targetCurrency' => strtoupper($targetCurrency),
        ]);
    }

    public function findActive(): array
    {
        return $this->findBy(['active' => true]);
    }

    public function save(CurrencyPair $pair): void
    {
        $em = $this->getEntityManager();
        $em->persist($pair);
        $em->flush();
    }
}

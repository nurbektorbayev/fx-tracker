<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Application\Port\Persistence\ExchangeRateRepositoryInterface;
use App\Domain\Entity\CurrencyPair;
use App\Domain\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ExchangeRateRepository extends ServiceEntityRepository implements ExchangeRateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function save(ExchangeRate $rate): void
    {
        $em = $this->getEntityManager();
        $em->persist($rate);
        $em->flush();
    }

    public function findLatestForPairAt(CurrencyPair $pair, \DateTimeImmutable $at): ?ExchangeRate
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pair = :pair')
            ->andWhere('r.validAt <= :at')
            ->setParameter('pair', $pair)
            ->setParameter('at', $at)
            ->orderBy('r.validAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

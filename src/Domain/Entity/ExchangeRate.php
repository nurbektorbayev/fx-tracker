<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exchange_rates')]
#[ORM\Index(name: 'idx_pair_valid_at', columns: ['pair_id', 'valid_at'])]
class ExchangeRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CurrencyPair::class)]
    #[ORM\JoinColumn(name: 'pair_id', nullable: false, onDelete: 'CASCADE')]
    private CurrencyPair $pair;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private string $rate;

    #[ORM\Column(length: 32)]
    private string $provider;

    #[ORM\Column(name: 'fetched_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $fetchedAt;

    #[ORM\Column(name: 'valid_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $validAt;

    public function __construct(
        CurrencyPair $pair,
        string $rate,
        string $provider,
        \DateTimeImmutable $fetchedAt,
        \DateTimeImmutable $validAt,
    ) {
        $this->pair      = $pair;
        $this->rate      = $rate;
        $this->provider  = $provider;
        $this->fetchedAt = $fetchedAt;
        $this->validAt   = $validAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPair(): CurrencyPair
    {
        return $this->pair;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getFetchedAt(): \DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function getValidAt(): \DateTimeImmutable
    {
        return $this->validAt;
    }
}

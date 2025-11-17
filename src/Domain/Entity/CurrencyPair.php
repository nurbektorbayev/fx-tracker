<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'currency_pairs')]
#[ORM\UniqueConstraint(
    name: 'uniq_currency_pair',
    columns: ['base_currency', 'target_currency']
)]
class CurrencyPair
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'base_currency', length: 3)]
    private string $baseCurrency;

    #[ORM\Column(name: 'target_currency', length: 3)]
    private string $targetCurrency;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string              $baseCurrency,
        string              $targetCurrency,
        bool                $active = true,
        ?\DateTimeImmutable $createdAt = null,
    )
    {
        $this->baseCurrency = strtoupper($baseCurrency);
        $this->targetCurrency = strtoupper($targetCurrency);
        $this->active = $active;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');

        if ($this->baseCurrency === $this->targetCurrency) {
            throw new \InvalidArgumentException('Base and target currencies must be different.');
        }

        if (!preg_match('/^[A-Z]{3}$/', $this->baseCurrency) ||
            !preg_match('/^[A-Z]{3}$/', $this->targetCurrency)) {
            throw new \InvalidArgumentException('Currency code must be a 3-letter code.');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

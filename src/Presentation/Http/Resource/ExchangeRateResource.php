<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Domain\Entity\ExchangeRate;

final readonly class ExchangeRateResource implements \JsonSerializable
{
    public function __construct(
        private ExchangeRate $rate,
        private string $base,
        private string $target,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'base'       => $this->base,
            'target'     => $this->target,
            'rate'       => $this->rate->getRate(),
            'valid_at'   => $this->rate->getValidAt()->format(DATE_ATOM),
            'fetched_at' => $this->rate->getFetchedAt()->format(DATE_ATOM),
            'provider'   => $this->rate->getProvider(),
        ];
    }
}

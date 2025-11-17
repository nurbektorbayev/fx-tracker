<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request;

use App\Application\UseCase\GetExchangeRate\GetExchangeRateRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetExchangeRateHttpRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Base currency is required.')]
        #[Assert\Length(
            min: 3,
            max: 3,
            exactMessage: 'Base currency must be exactly {{ limit }} characters long.'
        )]
        #[Assert\Regex(
            pattern: '/^[A-Za-z]{3}$/',
            message: 'Base currency must contain only letters.'
        )]
        public string  $base,

        #[Assert\NotBlank(message: 'Target currency is required.')]
        #[Assert\Length(
            min: 3,
            max: 3,
            exactMessage: 'Target currency must be exactly {{ limit }} characters long.'
        )]
        #[Assert\Regex(
            pattern: '/^[A-Za-z]{3}$/',
            message: 'Target currency must contain only letters.'
        )]
        public string  $target,

        // optional datetime in query (?at=2025-11-15T12:00:00Z)
        #[Assert\DateTime(
            format: 'Y-m-d\TH:i:sP',
            message: 'Parameter "at" must be a valid ISO8601 datetime (e.g. 2025-11-15T10:00:00Z).'
        )]
        public ?string $at = null,
    ) {
    }

    public function toUseCaseRequest(): GetExchangeRateRequest
    {
        $dateTime = $this->at !== null
            ? new \DateTimeImmutable($this->at)
            : new \DateTimeImmutable('now');

        return new GetExchangeRateRequest(
            strtoupper($this->base),
            strtoupper($this->target),
            $dateTime,
        );
    }
}

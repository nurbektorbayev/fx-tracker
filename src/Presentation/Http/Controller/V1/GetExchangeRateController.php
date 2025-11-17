<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\V1;

use App\Application\UseCase\GetExchangeRate\GetExchangeRateHandler;
use App\Presentation\Http\Request\GetExchangeRateHttpRequest;
use App\Presentation\Http\Resource\ExchangeRateResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final readonly class GetExchangeRateController
{
    public function __construct(private GetExchangeRateHandler $handler) {}

    #[Route('/rates', name: 'api_v1_get_rate', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/rates',
        summary: 'Get exchange rate for a currency pair',
        description: 'Returns the latest known exchange rate for a given currency pair.',
        tags: ['Exchange Rates'],
        parameters: [
            new OA\Parameter(
                name: 'base',
                description: 'Base currency code (e.g., USD)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'USD')
            ),
            new OA\Parameter(
                name: 'target',
                description: 'Target currency code (e.g., EUR)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'EUR')
            ),
            new OA\Parameter(
                name: 'at',
                description: 'Optional date/time to fetch historical rate (ISO8601)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: '2024-01-01T12:00:00Z')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Exchange rate found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'base', type: 'string', example: 'USD'),
                        new OA\Property(property: 'target', type: 'string', example: 'EUR'),
                        new OA\Property(property: 'rate', type: 'string', example: '0.92310000'),
                        new OA\Property(property: 'valid_at', type: 'string', example: '2025-01-01T12:00:00+00:00'),
                        new OA\Property(property: 'fetched_at', type: 'string', example: '2025-01-01T12:00:02+00:00'),
                        new OA\Property(property: 'provider', type: 'string', example: 'freecurrencyapi'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Rate not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Rate not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    public function __invoke(
        #[MapQueryString] GetExchangeRateHttpRequest $query
    ): JsonResponse
    {
        $useCaseRequest = $query->toUseCaseRequest();

        $rate = $this->handler->handle($useCaseRequest);

        if ($rate === null) {
            return new JsonResponse(['error' => 'Rate not found'], 404);
        }

        $resource = new ExchangeRateResource(
            $rate,
            $useCaseRequest->baseCurrency,
            $useCaseRequest->targetCurrency,
        );

        return new JsonResponse($resource);
    }
}

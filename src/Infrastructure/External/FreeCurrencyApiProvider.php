<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Application\Port\External\ExchangeRateProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FreeCurrencyApiProvider implements ExchangeRateProviderInterface
{
    private const string BASE_URL = 'https://api.freecurrencyapi.com/v1';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
        if ($this->apiKey === '') {
            throw new \InvalidArgumentException('API key is required');
        }
    }

    public function getLatestRates(string $baseCurrency, array $targets): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL.'/latest', [
            'query' => [
                'apikey'        => $this->apiKey,
                'base_currency' => strtoupper($baseCurrency),
                'currencies'    => implode(',', array_map('strtoupper', $targets)),
            ],
            'timeout' => 3.0,
        ]);

        $data = $response->toArray();

        $result = [];
        foreach ($data['data'] ?? [] as $code => $value) {
            $result[strtoupper($code)] = (string) $value;
        }

        return $result;
    }

    public function getProviderName(): string
    {
        return 'freecurrencyapi';
    }
}

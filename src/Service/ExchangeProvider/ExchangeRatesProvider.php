<?php
declare(strict_types=1);

namespace App\Service\ExchangeProvider;

use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesProvider implements ExchangeRateProviderInterface
{
    protected ?array $rates = null;

    public function __construct(
        protected readonly HttpClientInterface $exchangeRatesServiceClient
    ) {
    }

    protected function fetchExchangeRates(): ?array
    {
        $response = $this->exchangeRatesServiceClient->request('GET', '/latest');

        if ($response->getStatusCode() !== 200) {
            throw new HttpException($response->getStatusCode(), 'Unable to get rates');
        }

        $data = $response->toArray();

        if (!isset($data['rates'])) {
            throw new UnexpectedValueException('Invalid response format: missing "rates" key');
        }

        return $data['rates'];
    }

    public function getRates(): array
    {
        if (is_null($this->rates)) {
            return $this->refreshRates();
        }

        return $this->rates;
    }

    public function refreshRates(): array
    {
        $this->rates = $this->fetchExchangeRates();

        return $this->rates;
    }
}

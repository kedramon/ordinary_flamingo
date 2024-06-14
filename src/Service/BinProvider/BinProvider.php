<?php
declare(strict_types=1);

namespace App\Service\BinProvider;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinProvider implements BinProviderInterface
{
    public function __construct(
        protected readonly HttpClientInterface $binListServiceClient
    ) {
    }

    public function fetchCountryCodeFromBin(string $bin): string
    {
        $response = $this->binListServiceClient->request('GET', '/' . $bin);

        if ($response->getStatusCode() === 429) {
            throw new HttpException(429, 'Request limit is 5 per hour');
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedValueException('Unable to get bin details');
        }

        $data = $response->toArray();

        return $this->getCountryCode($data);
    }

    public function getCountryCode(array $data): string
    {
        if (array_key_exists('country', $data) && array_key_exists('alpha2', $data['country'])) {
            return $data['country']['alpha2'];
        }

        throw new InvalidArgumentException('Invalid data format, does it changed?');
    }
}

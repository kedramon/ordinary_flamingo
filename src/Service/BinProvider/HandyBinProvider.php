<?php
declare(strict_types=1);

namespace App\Service\BinProvider;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HandyBinProvider implements BinProviderInterface
{
    public function __construct(
        protected readonly HttpClientInterface $handyBinServiceClient
    ) {
    }

    public function fetchCountryCodeFromBin(string $bin): string
    {
        $response = $this->handyBinServiceClient->request('GET', '/bin/' . $bin);

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedValueException('Unable to get bin details');
        }

        $data = $response->toArray();

        return $this->getCountryCode($data);
    }

    public function getCountryCode(array $data): string
    {
        if (array_key_exists('Country', $data) && array_key_exists('A2', $data['Country'])) {
            return $data['Country']['A2'];
        }

        throw new InvalidArgumentException('Invalid data format, does it changed?');
    }
}

<?php

declare(strict_types=1);

namespace App\Service\ExchangeProvider;

interface ExchangeRateProviderInterface
{
    public function getRates(): array;
    public function refreshRates(): array;
}

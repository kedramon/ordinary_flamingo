<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\EuCountryCode;

class CalculationService
{
    public function calculateAmount(float $amount, float $exchangeRate): float
    {
        return $amount / $exchangeRate;
    }

    public function calculateResult(float $amount, string $countryCode): float
    {
        return round($amount * ($this->isEU($countryCode) ? 0.01 : 0.02), 2);
    }

    public function isEU(string $countryCode): bool
    {
        return EuCountryCode::tryFrom($countryCode) !== null;
    }
}

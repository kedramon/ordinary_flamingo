<?php

declare(strict_types=1);

namespace App\Service\BinProvider;

interface BinProviderInterface
{
    public function fetchCountryCodeFromBin(string $bin): string;
    public function getCountryCode(array $data): string;
}

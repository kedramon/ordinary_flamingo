<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Transaction
{
    public function __construct(
        #[Assert\NotBlank()]
        private readonly string $bin,
        #[Assert\NotBlank()]
        private readonly float $amount,
        #[Assert\NotBlank()]
        private readonly string $currency,
    ) {
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}

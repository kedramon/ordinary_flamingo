<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\CalculationService;

class CalculationServiceTest extends TestCase
{
    private CalculationService $calculationService;

    protected function setUp(): void
    {
        $this->calculationService = new CalculationService();
    }

    public function testCalculateAmount()
    {
        $amount = 100.0;
        $exchangeRate = 1.2;
        $result = $this->calculationService->calculateAmount($amount, $exchangeRate);

        $this->assertEquals(83.33, round($result, 2));
    }

    public function testCalculateResultForEu()
    {
        $amount = 100.0;
        $countryCode = 'EU';
        $result = $this->calculationService->calculateResult($amount, $countryCode);

        $this->assertEquals(2.0, $result);
    }

    public function testCalculateResultForNonEu()
    {
        $amount = 100.0;
        $countryCode = 'US';
        $result = $this->calculationService->calculateResult($amount, $countryCode);

        $this->assertEquals(2.0, $result);
    }

    public function testIsEu()
    {
        $countryCode = 'DK';
        $result = $this->calculationService->isEU($countryCode);

        $this->assertTrue($result);
    }
}

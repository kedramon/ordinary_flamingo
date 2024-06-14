<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\BinProvider\BinProvider;
use App\Service\BinProvider\BinProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Service\TransactionService;
use App\Service\CalculationService;
use App\Service\TransactionParser;
use App\Service\ExchangeProvider\ExchangeRatesProvider;
use App\DTO\Transaction;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;
    private TransactionParser|MockObject $transactionParser;
    private ExchangeRatesProvider|MockObject $exchangeRatesProvider;
    private BinProviderInterface|MockObject $binProvider;

    protected function setUp(): void
    {
        $calculationService = new CalculationService();
        $this->binProvider = $this->createMock(BinProvider::class);
        $this->transactionParser = $this->createMock(TransactionParser::class);
        $this->exchangeRatesProvider = $this->createMock(ExchangeRatesProvider::class);

        $this->exchangeRatesProvider->method('getRates')->willReturn([
            'USD' => 1.0784,
            'GBP' => 0.84468,
            'JPY' => 169.58,
        ]);

        $this->transactionService = new TransactionService(
            $this->binProvider,
            $this->exchangeRatesProvider,
            $this->transactionParser,
            $calculationService
        );
    }

    public function testProcessTransactions()
    {
        $filePath = 'import/input.txt';

        $transaction1 = new Transaction(
            '45717360',
            100.0,
            'EUR'
        );
        $transaction2 = new Transaction(
            '516793',
            50.00,
            'USD'
        );
        $transaction3 = new Transaction(
            '45417360',
            10000.00,
            'JPY'
        );
        $transaction4 = new Transaction(
            '41417360',
            130.00,
            'USD'
        );
        $transaction5 = new Transaction(
            '4745030',
            2000.00,
            'GBP'
        );

        $this->transactionParser->method('parseRow')
            ->willReturnOnConsecutiveCalls(
                $transaction1,
                $transaction2,
                $transaction3,
                $transaction4,
                $transaction5
            );

        $this->binProvider->method('fetchCountryCodeFromBin')
            ->willReturnMap([
                ['45717360', 'DK'],
                ['516793', 'LT'],
                ['45417360', 'JP'],
                ['41417360', 'US'],
                ['4745030', 'GB'],
            ]);

        $this->exchangeRatesProvider->method('getRates')->willReturn(['USD' => 1.0784, 'GBP' => 0.84468, 'JPY' => 169.58]);

        $result = $this->transactionService->processTransactions($filePath);

        $this->assertCount(5, $result);
        $this->assertEquals([1.0, 0.46, 1.18, 2.41, 47.36], $result);
    }
}

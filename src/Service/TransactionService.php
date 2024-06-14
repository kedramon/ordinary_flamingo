<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\BinProvider\BinProviderInterface;
use App\Service\ExchangeProvider\ExchangeRatesProvider;

class TransactionService
{
    public function __construct(
        private BinProviderInterface $binProvider,
        private ExchangeRatesProvider $exchangeRatesProvider,
        private TransactionParser $transactionParser,
        private CalculationService $calculationService
    )
    {
    }

    public function processTransactions(string $filePath): array
    {
        $result = [];
        $fileContent = file_get_contents($filePath);
        $rates = $this->exchangeRatesProvider->getRates();

        foreach (explode(PHP_EOL, $fileContent) as $row) {
            if (strlen($row) === 0) {
                continue;
            }

            $transaction = $this->transactionParser->parseRow($row);
            $countryCode = $this->binProvider->fetchCountryCodeFromBin($transaction->getBin());

            if ($transaction->getCurrency() === 'EUR' || !array_key_exists($transaction->getCurrency(), $rates)) {
                $exchangeRate = 1;
            } else {
                $exchangeRate = $rates[$transaction->getCurrency()];
            }

            $amount = $this->calculationService->calculateAmount($transaction->getAmount(), $exchangeRate);
            $result[] = $this->calculationService->calculateResult($amount, $countryCode);
        }

        return $result;
    }
}

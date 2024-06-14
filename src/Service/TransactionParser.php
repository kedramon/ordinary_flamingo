<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\Transaction;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TransactionParser
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function parseRow(string $row): ?Transaction
    {
        try {
            /** @var Transaction $transaction */
            $transaction = $this->serializer->deserialize($row, Transaction::class, 'json');
        } catch (\Exception $e) {
            return null;
        }

        /** @var ConstraintViolationListInterface $errors */
        $errors = $this->validator->validate($transaction);

        if (count($errors) > 0) {
            // Handle validation errors here.
            return null;
        }

        return $transaction;
    }
}

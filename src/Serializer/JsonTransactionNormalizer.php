<?php

declare(strict_types=1);

namespace App\Serializer;

use App\DTO\Transaction;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class JsonTransactionNormalizer implements DenormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    const ALREADY_CALLED = 'TRANSACTION_NORMALIZER_ALREADY_CALLED';

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[static::ALREADY_CALLED] = true;

        if (isset($data['amount']) && is_string($data['amount'])) {
            $data['amount'] = (float) $data['amount'];
        }

        return $this->normalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        if (array_key_exists(static::ALREADY_CALLED, $context)) {
            return false;
        }

        return Transaction::class === $type;
    }
}

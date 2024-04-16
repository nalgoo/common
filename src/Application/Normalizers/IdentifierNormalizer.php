<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Normalizers;

use Nalgoo\Common\Domain\IntegerIdentifier;
use Nalgoo\Common\Domain\StringIdentifier;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class IdentifierNormalizer implements DenormalizerInterface
{

	public function getSupportedTypes(?string $format): array
	{
		return [
			StringIdentifier::class => true,
			IntegerIdentifier::class => true,
		];
	}

	public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
	{
		if (!$this->supportsDenormalization($data, $type)) {
			throw new InvalidArgumentException();
		}

		return new $type($data);
	}

	public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
	{
		return (is_subclass_of($type, StringIdentifier::class) && is_string($data))
			|| (is_subclass_of($type, IntegerIdentifier::class) && is_int($data));
	}
}

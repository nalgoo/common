<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Normalizers;

use Nalgoo\Common\Domain\Enums\Gender;
use Nalgoo\Common\Domain\Exceptions\DomainLogicException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GenderNormalizer implements NormalizerInterface, DenormalizerInterface
{
	/**
	 * @TODO int, bool, and regular text support
	 * @throws DomainLogicException
	 */
	public function normalize($object, string $format = null, array $context = []): string
	{
		if (!$object instanceof Gender) {
			throw new InvalidArgumentException('The object must be instance of Gender!');
		}

		return $object->toString();
	}

	public function getSupportedTypes(?string $format): array
	{
		return [
			Gender::class => true
		];
	}

	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof Gender;
	}

	public function denormalize($data, $type, $format = null, array $context = []): Gender
	{
		if (!$this->supportsDenormalization($data, $type)) {
			throw new InvalidArgumentException();
		}

		return Gender::fromValue($data);
	}

	public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
	{
		return (is_string($data) || is_int($data) || is_bool($data)) && $type === Gender::class;
	}
}

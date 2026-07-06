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
	 *
	 * @param array<string, mixed> $context
	 *
	 * @throws DomainLogicException
	 */
	public function normalize(mixed $object, ?string $format = null, array $context = []): string
	{
		if (!$object instanceof Gender) {
			throw new InvalidArgumentException('The object must be instance of Gender!');
		}

		return $object->toString();
	}

	/**
	 * @return array<class-string|'*'|'object'|string, bool|null>
	 */
	public function getSupportedTypes(?string $format): array
	{
		return [
			Gender::class => true
		];
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof Gender;
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Gender
	{
		if (!$this->supportsDenormalization($data, $type)) {
			throw new InvalidArgumentException();
		}

		return Gender::fromValue($data);
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
	{
		return (is_string($data) || is_int($data) || is_bool($data)) && $type === Gender::class;
	}
}

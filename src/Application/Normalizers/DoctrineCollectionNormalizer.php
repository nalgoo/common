<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Normalizers;

use ArrayObject;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes doctrine collection into arrays without keys
 */
class DoctrineCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
	use NormalizerAwareTrait;

	public const SERIALIZE_COLLECTION_WITHOUT_KEYS = 'serialize-collection-without-keys';

	public function __construct(
		protected bool $useAsDefault = true
	)
	{
	}

	/**
	 * @return array<class-string|'*'|'object'|string, bool|null>
	 */
	public function getSupportedTypes(?string $format): array
	{
		return [
			Collection::class => $this->useAsDefault
		];
	}

	/**
	 * @param array<string, mixed> $context
	 *
	 * @return array<mixed>|\ArrayObject<string, mixed>|bool|float|int|string|null
	 *
	 * @throws ExceptionInterface
	 */
	public function normalize($object, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
	{
		if (!$object instanceof Collection || !$this->supportsNormalization($object, $format, $context)) {
			throw new InvalidArgumentException('The object must be instance of doctrine Collection!');
		}

		return $this->normalizer->normalize($object->getValues(), $format, $context);
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function supportsNormalization($data, ?string $format = null, array $context = []): bool
	{
		return ($context[self::SERIALIZE_COLLECTION_WITHOUT_KEYS] ?? $this->useAsDefault) && $data instanceof Collection;
	}
}

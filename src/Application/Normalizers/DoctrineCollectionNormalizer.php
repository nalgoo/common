<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Normalizers;

use Doctrine\Common\Collections\Collection;
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

	public const CONTEXT_KEY = 'serialize-collection-without-keys';

	public function __construct(
		protected bool $useAsDefault = true
	)
	{
	}

	public function getSupportedTypes(?string $format): array
	{
		return [
			Collection::class => $this->useAsDefault
		];
	}

	public function normalize($object, string $format = null, array $context = []): mixed
	{
		if (!$this->supportsNormalization($object, $format)) {
			throw new InvalidArgumentException('The object must be instance of doctrine Collection!');
		}

		return $this->normalizer->normalize($object->getValues(), $format, $context);
	}

	public function supportsNormalization($data, string $format = null, array $context = []): bool
	{
		return ($context[static::CONTEXT_KEY] ?? $this->useAsDefault) && $data instanceof Collection;
	}
}

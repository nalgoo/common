<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Normalizers;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer as SymfonyPropertyNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Decorator for Symfony's PropertyNormalizer that handles Doctrine proxy objects.
 *
 * Uses composition instead of inheritance because Symfony's PropertyNormalizer
 * is final since symfony/serializer 7.0.
 *
 * Wraps a fully configured SymfonyPropertyNormalizer instance and adds:
 * - Lazy-loading of uninitialized Doctrine proxies before normalization
 * - Filtering of internal __-prefixed proxy properties (Doctrine ORM 2.x)
 **/
class PropertyNormalizer implements NormalizerInterface, SerializerAwareInterface
{
	public function __construct(
		private readonly SymfonyPropertyNormalizer $normalizer,
	) {
	}

	/**
	 * @param array<string, mixed> $context
	 *
	 * @return array<mixed>|\ArrayObject<string, mixed>|bool|float|int|string|null
	 */
	public function normalize(mixed $object, ?string $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
	{
		if (interface_exists(\Doctrine\Persistence\Proxy::class) && ($object instanceof \Doctrine\Persistence\Proxy)) {
			if (!$object->__isInitialized()) {
				$object->__load();
			}

			$context[AbstractNormalizer::IGNORED_ATTRIBUTES] = array_merge(
				$context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [],
				['__initializer__', '__cloner__', '__isInitialized__'],
			);
		}

		return $this->normalizer->normalize($object, $format, $context);
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $this->normalizer->supportsNormalization($data, $format, $context);
	}

	/**
	 * @return array<class-string|'*'|'object'|string, bool|null>
	 */
	public function getSupportedTypes(?string $format): array
	{
		return $this->normalizer->getSupportedTypes($format);
	}

	/**
	 * Required so that Symfony's Serializer injects itself into the inner normalizer,
	 * which needs it to recursively handle nested objects.
	 */
	public function setSerializer(SerializerInterface $serializer): void
	{
		$this->normalizer->setSerializer($serializer);
	}
}

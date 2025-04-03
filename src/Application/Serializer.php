<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application;

use Nalgoo\Common\Application\Exceptions\DeserializeException;
use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Webmozart\Assert\Assert;

class Serializer implements SerializerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

	const FORMAT = 'json';

    public function __construct(\Symfony\Component\Serializer\SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    public function serialize(mixed $data, ?array $groups = null): string
    {
		$context = [];

		if (!is_null($groups)) {
			Assert::allStringNotEmpty($groups, 'Serializer groups must be array of strings!');
			$context['groups'] = $groups;
		}

		return $this->serializer->serialize($data, static::FORMAT, $context);
    }

    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    public function deserialize(mixed $data, string $type): object|array
    {
		try {
			return $this->serializer->deserialize($data, $type, static::FORMAT);
		} catch (\Throwable $e) {
			throw new DeserializeException('Deserialization failed: ' . $e->getMessage(), 0, $e);
		}
    }
}

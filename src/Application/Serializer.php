<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application;

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

    public function serialize($data, ?array $groups = null): string
    {
		Assert::allStringNotEmpty($groups, 'Serializer groups must be array of strings!');
		return $this->serializer->serialize($data, static::FORMAT, !is_null($groups) ? ['groups' => $groups] : []);
    }

    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    public function deserialize($data, string $type,): object
    {
        return $this->serializer->deserialize($data, $type, static::FORMAT);
    }
}

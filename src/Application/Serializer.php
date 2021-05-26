<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application;

use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class Serializer implements SerializerInterface, SerializerAwareInterface, \Symfony\Component\Serializer\SerializerInterface
{
    use SerializerAwareTrait;

    public function __construct(\Symfony\Component\Serializer\SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    public function serialize($data, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    public function deserialize($data, string $type, string $format = 'json', array $context = []): object
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}

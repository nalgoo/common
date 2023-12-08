<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence\DoctrineTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BinaryType;

/**
 * IP address type for storing ip addresses (IPV4 and IPV6) in varbinary columns
 */
class IPAddressType extends BinaryType
{
	const NAME = 'ip_address';

	/**
	 * {@inheritdoc}
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		$value = is_resource($value) ? stream_get_contents($value) : $value;

		return is_null($value) ? null : inet_ntop($value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return is_null($value) ? null : inet_pton($value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName(): string
	{
		return static::NAME;
	}
}

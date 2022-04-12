<?php

namespace Nalgoo\Common\Domain\Enums;

use Nalgoo\Common\Domain\IntValueInterface;

/**
 * @deprecated use build in enums !
 */
abstract class IntEnum extends Enum implements IntValueInterface
{
	public static function fromInt(int $value): static
	{
		return static::getInstanceFor($value);
	}

	public function toInt(): int
	{
		return $this->value;
	}

	/**
	 * @deprecated Deprecated because of naming consistency, use toInt() instead
	 */
	public function asInt(): int
	{
		return $this->toInt();
	}
}

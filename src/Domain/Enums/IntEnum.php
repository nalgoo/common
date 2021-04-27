<?php

namespace Nalgoo\Common\Domain\Enums;

abstract class IntEnum extends Enum implements IntEnumInterface
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

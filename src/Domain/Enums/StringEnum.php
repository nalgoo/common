<?php

namespace Nalgoo\Common\Domain\Enums;

abstract class StringEnum extends Enum implements StringEnumInterface, \Stringable
{
	public static function fromString(string $value): static
	{
		return static::getInstanceFor($value);
	}

	public function toString(): string
	{
		return $this->value;
	}

	/**
	 * @deprecated Deprecated because of naming consistency, use toString() instead
	 */
	public function asString(): string
	{
		return $this->toString();
	}

	public function __toString(): string
	{
		return $this->toString();
	}
}

<?php

namespace Nalgoo\Common\Domain\Enums;

use Nalgoo\Common\Domain\StringValueInterface;

/**
 * @deprecated use build in enums !
 */
abstract class StringEnum extends Enum implements StringValueInterface, \Stringable
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

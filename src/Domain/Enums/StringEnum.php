<?php

namespace Nalgoo\Common\Domain\Enums;

abstract class StringEnum extends Enum implements StringEnumInterface
{
	public function __construct(string $value)
	{
		parent::__construct($value);
	}

	public static function fromString(string $value): static
	{
		return new static($value);
	}

	public function asString(): string
	{
		return $this->value;
	}

}

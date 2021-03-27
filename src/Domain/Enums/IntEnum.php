<?php

namespace Nalgoo\Common\Domain\Enums;

abstract class IntEnum extends Enum implements IntEnumInterface
{
	public function __construct(int $value)
	{
		parent::__construct($value);
	}

	public static function fromInt(int $value): static
    {
		return new static($value);
	}

	public function asInt(): int
	{
		return $this->value;
	}

}

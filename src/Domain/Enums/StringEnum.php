<?php

namespace Nalgoo\Common\Domain\Enums;

abstract class StringEnum extends Enum
{
	public function __construct(string $value)
	{
		parent::__construct($value);
	}

	public static function fromString(string $value)
	{
		return new static($value);
	}

	public function asString(): string
	{
		return $this->value;
	}

}

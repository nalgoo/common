<?php

namespace Nalgoo\Common\Domain;

class IntEnum extends Enum
{
	public function __construct(int $value)
	{
		parent::__construct($value);
	}

	public static function fromInt(int $value)
	{
		return new static($value);
	}

	public function asInt(): string
	{
		return $this->value;
	}

}

<?php

namespace Nalgoo\Common\Application\DTO;

class NamedValue
{
	public function __construct(
		protected \BackedEnum $name,
		protected mixed $value
	)
	{
	}

	public function getName(): \BackedEnum
	{
		return $this->name;
	}

	public function getValue(): mixed
	{
		return $this->value;
	}
}

<?php

namespace Nalgoo\Common\Application\DTO;

class NamedValue
{
	protected \BackedEnum $name;

	protected mixed $value;

	public function __construct(\BackedEnum $name, mixed $value)
	{
		$this->value = $value;
		$this->name = $name;
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

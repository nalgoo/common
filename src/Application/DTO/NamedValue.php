<?php

namespace Nalgoo\Common\Application\DTO;

class NamedValue
{
	protected \StringBackedEnum $name;

	protected mixed $value;

	public function __construct(\StringBackedEnum $name, mixed $value)
	{
		$this->value = $value;
		$this->name = $name;
	}

	public function getName(): \StringBackedEnum
	{
		return $this->name;
	}

	public function getValue(): mixed
	{
		return $this->value;
	}
}

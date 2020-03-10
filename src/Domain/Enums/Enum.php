<?php

namespace Nalgoo\Common\Domain\Enums;

use Webmozart\Assert\Assert;

abstract class Enum
{
	protected $value;

	public function __construct($value)
	{
		Assert::oneOf($value, static::getConstants());

		$this->value = $value;
	}

	protected static function getConstants(): array
	{
		$reflection = new \ReflectionClass(static::class);

		return array_values($reflection->getConstants());
	}

}
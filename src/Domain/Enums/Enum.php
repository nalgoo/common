<?php

namespace Nalgoo\Common\Domain\Enums;

use Webmozart\Assert\Assert;

abstract class Enum
{
	protected mixed $value;

	protected static array $instances = [];

	private function __construct($value)
	{
		Assert::oneOf($value, static::getConstants());

		$this->value = $value;
	}

	protected static function getConstants(): array
	{
		$reflection = new \ReflectionClass(static::class);

		return array_values($reflection->getConstants());
	}

	protected static function getInstanceFor($value): static
	{
		if (!isset(static::$instances[$value])) {
			static::$instances[$value] = new static($value);
		}

		return static::$instances[$value];
	}
}

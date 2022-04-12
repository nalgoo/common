<?php

namespace Nalgoo\Common\Domain\Enums;

use ReflectionClassConstant;
use Webmozart\Assert\Assert;

/**
 * @deprecated use build in enums !
 */
abstract class Enum implements \JsonSerializable
{
	protected mixed $value;

	private function __construct($value)
	{
		Assert::oneOf($value, static::getConstants());

		$this->value = $value;
	}

	protected static function getConstants(): array
	{
		$reflection = new \ReflectionClass(static::class);

		return array_values($reflection->getConstants(ReflectionClassConstant::IS_PUBLIC));
	}

	protected static function getInstanceFor($value): static
	{
		static $instances = [];

		if (!isset($instances[static::class][$value])) {
			$instances[static::class][$value] = new static($value);
		}

		return $instances[static::class][$value];
	}

	public function jsonSerialize(): mixed
	{
		return $this->value;
	}
}

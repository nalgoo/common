<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain\Enums;

abstract class NullableStringEnum extends Enum
{
	public function __construct(?string $value)
	{
		parent::__construct($value);
	}

	protected static function getConstants(): array
	{
		return array_merge(parent::getConstants(), null);
	}

	public static function fromString(?string $value)
	{
		return new static($value);
	}

	public function asString(): ?string
	{
		return $this->value;
	}
}

<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain\Enums;

/**
 * @deprecated This does not make sense, please don't use it, sorry :)
 */
abstract class NullableStringEnum extends Enum
{
	public function __construct(?string $value)
	{
		parent::__construct($value);
	}

	protected static function getConstants(): array
	{
		return array_merge(parent::getConstants(), [null]);
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

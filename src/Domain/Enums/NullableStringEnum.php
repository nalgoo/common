<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain\Enums;

/**
 * @deprecated This does not make sense, please don't use it, sorry :)
 */
abstract class NullableStringEnum extends Enum
{
	protected static function getConstants(): array
	{
		return array_merge(parent::getConstants(), [null]);
	}

	public static function fromString(?string $value): static
	{
		return static::getInstanceFor($value);
	}

	public function asString(): ?string
	{
		return $this->value;
	}
}

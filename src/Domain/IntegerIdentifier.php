<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

class IntegerIdentifier implements IntValueInterface, \JsonSerializable
{
	private int $id;

	public function __construct(int $id)
	{
		$this->id = $id;
	}

	/** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
	public static function fromInt(int $id): static
	{
		return new static($id);
	}

	public function toInt(): int
	{
		return $this->id;
	}

	public function sameAs(IntegerIdentifier $identifier): bool
	{
		return get_class($this) === get_class($identifier) && $this->id === $identifier->toInt();
	}

	public function jsonSerialize()
	{
		return $this->id;
	}
}
